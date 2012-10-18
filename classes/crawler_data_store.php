<?php

//include_once( "config.php" );

/*
Class: CrawlerDataStore
Author: Joe Bergevin (joe@seo.com)
Purpose: Process the $DocInfo object which is created in the PHPCrawler class created by Uwe Hunfeld (phpcrawl@cuab.de).
The process includes storing all necessary information from the $DocInfo object in an external database.
*/
class CrawlerDataStore {
	//define variables to be stored in
	private $rebuilt_url, $file_size, $doc_type, $response_code, $load_time, $error_details, $error_code, $orig_url_id,
			$server, $page_id, $content, $title, $mysqli, $url_id_list, $anchor_id_list, $headings, $a_tags, $link_tags, 
			$img_tags;
	
	/*
	Function: __construct
	Expects: $DocInfo: object created from the PHPCrawler class, containing the elements from a given url.
	Purpose: Process the $DocInfo object and save all the info in the DB.
	*/
	public function __construct( $DocInfo, $seocom_info ) {
		$this->mysqli = $this->create_sql_var();
		$this->crawl_id = 4;
		$this->a_tags = array( );
		$this->link_tags = array( );
		$this->img_tags = array( );
		
		// first, process the $seocom_info items
		foreach ( $seocom_info as $key => $value ) {
			switch ( $key ) {
				case 'title': 		$this->title 		= $value; 	break;
				case 'server': 		$this->server 		= $value; 	break;
				case 'file_size': 	$this->file_size 	= $value; 	break;
				case 'headings': 	$this->headings		= $value; 	break;
				case 'a_tags': 		$this->a_tags		= $value; 	break;
				case 'link_tags': 	$this->link_tags	= $value; 	break;
				case 'img_tags': 	$this->img_tags		= $value; 	break;
			}
		}

		// next, process the link items
		$this->rebuilt_url = $DocInfo->url;
		$this->process_links( );
		// $this->process_headings( );

		// next, process the $DocInfo items
		foreach ( $DocInfo as $key => $DocItem ) {
			switch ( $key ) {
				case 'bytes_received': 		$this->size 			= $DocItem; 	break;
				case 'content_type': 		$this->doc_type 		= $DocItem; 	break;
				case 'content': 			$this->content 			= $this->mysqli->real_escape_string( 
																	  $DocItem ); 	break;
				case 'http_status_code': 	$this->response_code 	= $DocItem; 	break;
				case 'data_transfer_time': 	$this->load_time 		= $DocItem; 	break;
				case 'error_occured': 		$this->error_details 	= $DocItem; 	break;
				case 'error_code': 			$this->error_code 		= $DocItem; 	break;
				case 'error_string': 		$this->error_details   .= " | $DocItem"; break;
				// case 'links_found': $this->process_links( $DocItem ); break; 			//array
				// case 'cookies':	$this->process_cookies( $DocItem ); break;			//array
				case 'meta_attributes': 	$this->process_page_meta( $DocItem ); 	break; 	//array
				
			} // end of switch statement on $DocItem
		} // end of foreach loop on $DocInfo

		$this->store_vars_in_db();
	
	} // end of __construct function
	
	/*
	Function: process_cookies
	Expects: $cookies: list of cookies for the crawled page
	Purpose: Store the cookie info in the DB
	*/
	private function process_cookies( $cookies ) {
		if ( count( $cookies ) == 0 ) return;
		$cookies_list = array();
		foreach ( $cookies as $cookie ) {
			$cookies_list[] = "( '". $this->page_id ."', '$key', '$value' )";
			
			//INSERT INTO `cookies` ( `page_id`, `name` ) VALUES ( '15', 'cookie name' )	
		}
		
	} // end of process_cookies function
	

	/****************************************************************************
	FUNCTION SET: PROCESS_LINKS
	Purpose: put all link data in the database and return corresponding page_id's
	Functions in Set: 
		1. process_links - main function of group
		2. clean_anchor
		3. get_ids
			a. get_url_ids
			b. get_anchor_ids
			c. array_clean_unique
		4. multidim_array_search - function to search a multi-dimensional array
		5. implode_with_escape - same as implode function but also deals with escape characters
	*/
	
	/*
	Function: process_links (member of PROCESS_LINKS)
	Expects: $links_group: list of links from $DocInfo
	Purpose: Loop through the list of links ($links_group) and get url_id's and anchor_id's for all links.
	Returns: false if $links_group is empty
	*/
	private function process_links( ) {
		if ( !$this->a_tags && !$this->link_tags && !$this->img_tags ) return false;
		
		$this->get_ids( );
		
		$list_of_a_inserts    = array( );
		$list_of_link_inserts = array( );
		$list_of_img_inserts  = array( );
		
		// process the img_tags list and create list of inserts for sql string
		$page_id = $this->page_id;
		if ( $this->img_tags ) {
			$list_of_img_metas = array( );
			foreach ( $this->img_tags as $img_obj ) {
				foreach ( $img_obj as $key => $value ) {
					switch ( $key ) {
						case 'url_rebuild':
							$url_id = $this->multidim_array_search( $value, $this->url_id_list, 'url_id' );
							break;
						case 'linkcode':
							$raw_img = $this->mysqli->real_escape_string( $value );
							break;
						case 'src':
							$raw_src = $value;
							break;
						default:
							// replace the a_tag_id later when you have inserted the a_tag
							$list_of_img_metas[] = array( 'img_tag' => $img_obj['linkcode'], 
														  'key' 	=> $key, 
														  'value' 	=> $value );
							break;
					} // end of switch statement
				} // end of foreach $img_obj loop
				
				if ( $url_id ) $list_of_img_inserts[] = 
					"( '$url_id', '$raw_src', '$raw_img', '$page_id' )";
			} // end of foreach img_tags loop

			if ( $list_of_img_inserts ) {
				$insert_values_imploded = implode( ", ", $list_of_img_inserts );
				//one sql query to insert all the img_tags texts in one shot
				$sql = "INSERT INTO cr_imgs 
							( `url_id`, `raw_src`, `raw_img`, `page_id` )
						VALUES $insert_values_imploded";
				$this->mysqli->query( $sql );
			}
			$sql = "SELECT `img_id`, `raw_img` FROM `cr_imgs`";
			$img_id_list = $this->sql_result_to_array( $sql );

			// use this array to complete the inserts for the meta tags.
			if ( $list_of_img_metas ) {
				$list_of_img_meta_inserts = array( );
				foreach ( $list_of_img_metas as $meta_tag ) {
					$img_id = $this->multidim_array_search( $meta_tag['img_tag'], $img_id_list, 'img_id' );
					if ( $img_id ) { // && !is_array( $meta_tag )
						$list_of_img_meta_inserts[] = "( '$img_id', '" 
													     .$meta_tag['key'] ."', '" 
													     .$meta_tag['value'] ."' )"; 
					}

				}
				unset($list_of_img_metas);					
				if ( $list_of_img_meta_inserts ) {
					$insert_values_imploded = implode( ", ", $list_of_img_meta_inserts );
					//one sql query to insert all the a_tags in one shot
					$sql = "INSERT INTO cr_img_meta 
								( `img_id`, `key`, `value` ) 
							VALUES $insert_values_imploded";
					$this->mysqli->query( $sql );
				}

				// unset( $img_id_list ); don't unset this til after it's been used for the a_tags
				unset( $list_of_img_meta_inserts ); unset( $insert_values_imploded );
			}
		}

		// process the a_tags list and create list of inserts for sql string
		if ( $this->a_tags ) {
			$list_of_a_tag_metas = array( );
			foreach ( $this->a_tags as $link_obj ) {
				$img_id = NULL;
				foreach ( $link_obj as $key => $value ) {
					switch ( $key ) {
						case 'anchor':
							$anchor_id = $this->multidim_array_search( $this->clean_anchor( $link_obj['anchor'] ), 
															   $this->anchor_id_list, 'anchor_id' );
							break;
						case 'url_rebuild':
							$url_id = $this->multidim_array_search( $link_obj['url_rebuild'], $this->url_id_list, 'url_id' );
							$relative = !( $link_obj['href'] == $value );
							break;
						case 'linkcode':
							$raw_a_tag = $this->mysqli->real_escape_string( $value );
							break;
						case 'href':
							$raw_href = $this->mysqli->real_escape_string( $value );
							break;
						default:
							if ( is_array( $value ) ) {
								// find the img_id for it and store it.
								// in the future, you may need to come up with a more reliable way of isolating an image tag
								$child_tag = $value[0];
								if ( substr( $child_tag, 0, 4 ) == "<img" ) {
									$img_id = $this->multidim_array_search( $child_tag, $img_id_list, 'img_id' );
								}
							} else {	
							$list_of_a_tag_metas[] = array( 'a_tag' => $link_obj['linkcode'], 
															'key' 	=> $key, 
															'value' => $value );
							}


							break;
					}
					
					$page_id = $this->orig_url_id;
				}
				if ( $url_id ) $list_of_a_inserts[] = 
						"( '$url_id', '$raw_href', '$relative', '$anchor_id', '$raw_a_tag', '$img_id', '$page_id' )";
			}

			if ( $list_of_a_inserts ) {
				$insert_values_imploded = implode( ", ", $list_of_a_inserts );
				//one sql query to insert all the a_tags in one shot
				$sql = "INSERT INTO cr_a_tags 
							( `url_id`, `raw_href`, `relative`, `anchor_id`, `raw_anchor`, `img_id`, `page_id` ) 
						VALUES $insert_values_imploded";
				$this->mysqli->query( $sql );
			}
		}

		// download the list of a_tags and their id's to an array
		$sql = "SELECT `a_tag_id`, `raw_anchor` FROM `cr_a_tags`";
		$a_tag_id_list = $this->sql_result_to_array( $sql );
		
		// use this array to complete the inserts for the meta tags.
		if ( $list_of_a_tag_metas ) {
			$list_of_a_tag_meta_inserts = array( );
			foreach ( $list_of_a_tag_metas as $meta_tag ) {
				$a_tag_id = $this->multidim_array_search( $meta_tag['a_tag'], $a_tag_id_list, 'a_tag_id' );
				if ( $a_tag_id && !is_array( $meta_tag['value'] )  ) { // 
					$list_of_a_tag_meta_inserts[] = "( '$a_tag_id', '" 
													   .$meta_tag['key'] ."', '" 
													   .$meta_tag['value'] ."' )"; 
				}
			}
			unset($list_of_a_tag_metas);
			if ( $list_of_a_tag_meta_inserts ) {
				$insert_values_imploded = implode( ", ", $list_of_a_tag_meta_inserts );
				//one sql query to insert all the a_tags in one shot
				$sql = "INSERT INTO cr_a_tag_meta 
							( `a_tag_id`, `key`, `value` ) 
						VALUES $insert_values_imploded";
				$this->mysqli->query( $sql );
			}

			// unset( $a_tag_id_list ); keep this for the headings...
			unset( $list_of_a_tag_meta_inserts ); unset( $insert_values_imploded );
		}

		// process the link_tags list and create list of inserts for sql string
		if ( $this->link_tags ) {
			$list_of_link_tag_metas = array( );
			foreach ( $this->link_tags as $link_obj ) {
				$page_id = $this->page_id;
				foreach ( $link_obj as $key => $value ) {
					switch ( $key ) {
						case 'url_rebuild':
							$url_id = $this->multidim_array_search( $value, $this->url_id_list, 'url_id' );
							$relative = !( $raw_href == $value );
							break;
						case 'linkcode':
							$raw_link_tag = $this->mysqli->real_escape_string( $value );
							break;
						case 'href':
							$raw_href = $value;
							break;
						default:
							// replace the link_tag_id later when you have inserted the a_tag
							$list_of_link_tag_metas[] = array( 'link_tag' => $link_obj['linkcode'], 
															'key' 	=> $key, 
															'value' => $value );
							break;
					}
				}
			
				if ( $url_id ) $list_of_link_inserts[] = 
					"( '$raw_link_tag', '$page_id', '$url_id' )";	
			}
			
			if ( $list_of_link_inserts ) {
				$insert_values_imploded = implode( ", ", $list_of_link_inserts );
				//one sql query to insert all the link_tags texts in one shot
				$sql = "INSERT INTO cr_link_tags 
							( `raw_link_tag`, `page_id`, `referer_page_id` ) 
						VALUES $insert_values_imploded";
				$this->mysqli->query( $sql );
			}
		}

		// download the list of link_tags and their id's to an array
		$sql = "SELECT `link_tag_id`, `raw_link_tag` FROM `cr_link_tags`";
		$link_tag_id_list = $this->sql_result_to_array( $sql );
		
		// use this array to complete the inserts for the meta tags.
		$list_of_link_tag_meta_inserts = array( );
		if ( $list_of_link_tag_metas ) {
			foreach ( $list_of_link_tag_metas as $meta_tag ) {
				$link_tag_id = $this->multidim_array_search( $meta_tag['link_tag'], $link_tag_id_list, 'link_tag_id' );
				if ( $link_tag_id ) { // && !is_array( $meta_tag )
					$list_of_link_tag_meta_inserts[] = "( '$link_tag_id', '" 
													   .$meta_tag['key'] ."', '" 
													   .$meta_tag['value'] ."' )"; 
				}
			}

			if ( $list_of_link_tag_meta_inserts ) {
				$insert_values_imploded = implode( ", ", $list_of_link_tag_meta_inserts );
				//one sql query to insert all the link_tags in one shot
				$sql = "INSERT INTO cr_link_tag_meta 
							( `link_tag_id`, `key`, `value` ) 
						VALUES $insert_values_imploded";
				$this->mysqli->query( $sql );
			}
		}
		unset( $link_tag_id_list ); unset( $list_of_link_tag_meta_inserts ); unset( $insert_values_imploded );
		

		// process the headings list and create list of inserts for sql string
		if ( $this->headings ) {
			$list_of_heading_inserts = array( );
			$a_tag_id = NULL;
			// $page_id = $this->page_id;
			foreach ( $this->headings as $h_obj ) {
				$level = NULL;
				$instance = NULL;
				$raw_header = NULL;
				$header_text = NULL;
				$a_tag_id = NULL;
				foreach ( $h_obj as $key => $value ) {
					switch ( $key ) {
						case 'level':
							$level = $value;
							break;
						case 'instance':
							$instance = $value;
							break;
						case 'raw_header':
							$raw_header = $this->mysqli->real_escape_string( $value );
							break;
						case 'header_text':
							$header_text = $this->clean_anchor( $value );
							break;
						case 'a_tag_raw':
							// if ( substr( $value, 0, 3 ) == "<a " ) {
							$a_tag_id = $this->multidim_array_search( $value, $a_tag_id_list, 'a_tag_id' );
								// echo "<p>A_TAG: $value | A_TAG_ID: $a_tag_id</p>";
								// print_r( $a_tag_id_list );
							// }
							break;
					}
					$list_of_heading_inserts[] = 
						"( '$level', '$instance', '$page_id', '$raw_header', '$a_tag_id', '$header_text' )";
				}
			}
			// print_r( $list_of_heading_inserts );
			if ( $list_of_heading_inserts ) {
				$insert_values_imploded = implode( ", ", $list_of_heading_inserts );
				//one sql query to insert all the headings in one shot
				$sql = "INSERT INTO cr_headings
							( `level`, `instance`, `page_id`, `raw_header`, `a_tag_id`, `header_text` ) 
						VALUES $insert_values_imploded";
				$this->mysqli->query( $sql );
			}
		}
	} // end of process_links function

	/*
	Function: clean_anchor (member of PROCESS_LINKS)
	Expects: $anchor: raw anchor text
	Returns: $anchor: cleaned-up
	*/
	private function clean_anchor( $anchor ) {
		$anchor = trim( strtolower( $anchor ) );
		$anchor = strip_tags( $anchor );
		$anchor = trim( $anchor );
		return $anchor;
	}
	
	/*
	Function: get_ids (member of PROCESS_LINKS)
	Expects: $links_group: one-dim array of the urls in the $links_group
	Purpose: Stores URLs and Anchor's in db and then populates the id's for them in $url_id_list and $anchor_id_list
	*/
	private function get_ids( ) {
		$list_of_anchors = array();
		$list_of_urls = array();
		$list_of_urls[] = $this->rebuilt_url; //to be able to have the url_id of the page these links are on.
		// $list_of_urls[] = $this->rebuilt_url; //to be able to have the url_id of the page these links are on.

		if ( $this->a_tags ) {
			foreach ( $this->a_tags as $link_obj ) {
				if ( $link_obj['linkcode'] ) {
					$list_of_urls[] = $link_obj['url_rebuild'];
					$list_of_anchors[] = $this->clean_anchor( $link_obj['anchor'] );
				}
			}
		}

		if ( $this->link_tags ) {
			foreach ( $this->link_tags as $link_obj ) {
				if ( $link_obj['linkcode'] ) $list_of_urls[] = $link_obj['url_rebuild'];
			}
		}

		if ( $this->img_tags ) {
			foreach ( $this->img_tags as $link_obj ) {
				if ( $link_obj['linkcode'] ) $list_of_urls[] = $link_obj['url_rebuild'];
			}
		}

		$list_of_urls = $this->array_clean_unique( $list_of_urls );
		if ( $list_of_anchors ) $list_of_anchors = $this->array_clean_unique( $list_of_anchors );
		
		//get url_id and anchor_id for every link in $links_group
		$this->get_url_ids( $list_of_urls );
		if ( $list_of_anchors ) $this->get_anchor_ids( $list_of_anchors );
		$this->orig_url_id = $this->multidim_array_search( $this->rebuilt_url, $this->url_id_list, 'url_id' );
		
		$sql = "INSERT INTO `cr_pages` ( `crawl_id`, `url_id` ) 
				VALUES ( '" .$this->crawl_id ."', '" .$this->orig_url_id ."' )";
		$this->mysqli->query( $sql );
		$this->page_id = $this->mysqli->insert_id;
	}
	
	/*
	Function: get_url_ids (sub-member of PROCESS_LINKS)
	Expects: $list_of_urls: one-dim array of the urls in the $links_group
	Returns: $list_of_urls: two-dim array urls given, and the corresponding url_id's
	*/
	private function get_url_ids( $list_of_urls ) {
		$list_of_urls = $this->array_clean_unique( $list_of_urls );
		$urls_imploded_select = "rebuilt_url='" 
			.$this->implode_with_escape( "' OR rebuilt_url='", $list_of_urls ) ."'";
		//looks like this: rebuilt_url='http://www.seo.com/xmlrpc.php' OR rebuilt_url='http://www.seo.com/'
		
		//query the db and get the rows
		$sql = "SELECT rebuilt_url FROM `cr_urls` WHERE $urls_imploded_select";
		$urls_in_db = array( );
		$urls_in_db = $this->sql_result_to_array( $sql, 'rebuilt_url' );
		
		if ( count( $urls_in_db ) != count( $list_of_urls ) ) { //if there are URLs that are not already in the DB
			//check which URLs are not in the db
			$urls_not_in_db = array_diff( $list_of_urls, $urls_in_db );
			
			//compile insert stmt containing the $urls_not_in_db
			$urls_imploded_insert = "( '" .$this->implode_with_escape( "' ), ( '", $urls_not_in_db ) ."' )";
			
			$sql = "INSERT INTO cr_urls ( `rebuilt_url` ) VALUES $urls_imploded_insert";
			$this->mysqli->query( $sql );
			// echo "<p>$sql</p>";
		}  // end of if statement
		$sql = "SELECT url_id, rebuilt_url FROM `cr_urls` WHERE $urls_imploded_select";
		$this->url_id_list = $this->sql_result_to_array( $sql );
	}
	
	/*
	Function: get_anchor_ids (sub-member of PROCESS_LINKS)
	Expects: $list_of_anchors: one-dim array of the anchor texts in the $links_group
	Returns: $list_of_anchors: two-dim array anchor texts given, and the corresponding anchors_id's
	*/
	private function get_anchor_ids( $list_of_anchors ) {
		$list_of_anchors = $this->array_clean_unique( $list_of_anchors );
		$anchors_imploded_select = "trimmed_anchor='" 
			.$this->implode_with_escape( "' OR trimmed_anchor='", $list_of_anchors ) ."'";
		//looks like this: trimmed_anchor='seo company' OR trimmed_anchor='seo' OR trimmed_anchor='etc'
		
		//query the db and get the rows
		$sql = "SELECT trimmed_anchor FROM `cr_anchors` WHERE $anchors_imploded_select";
		$anchors_in_db = $this->sql_result_to_array( $sql, 'trimmed_anchor' );
		
		$anchor_id_array = false;
		if ( count( $anchors_in_db ) != count( $list_of_anchors ) ) {
			//check which URLs are not in the db
			$anchors_not_in_db = array_diff( $list_of_anchors, $anchors_in_db );
			
			//compile insert stmt containing the $urls_not_in_db
			$anchors_imploded_insert = "( '" .$this->implode_with_escape( "' ), ( '", $anchors_not_in_db ) ."' )";
			
			$sql = "INSERT INTO cr_anchors ( trimmed_anchor ) VALUES $anchors_imploded_insert";
			$this->mysqli->query( $sql );
			
			$sql = "SELECT trimmed_anchor, anchor_id FROM `cr_anchors` WHERE $anchors_imploded_select";
			$this->anchor_id_list = $this->sql_result_to_array( $sql );
		} // end of if statement
	} // end of get_anchor_ids function
	
	/*
	Function: array_clean_unique (sub-member of PROCESS_LINKS)
	Expects: $array: one-dim array containing duplicates and un-trimmed data
	Returns: $array: one-dim unique array with trimmed data
	*/
	private function array_clean_unique( $array ) {
		if ( $array ) {
			foreach ( $array as $array_item )
				$array_item = trim( $array_item );
			$array = array_unique( $array );
		}
		return $array;
	}
	
	/*
	Function: multidim_array_search (member of PROCESS_LINKS)
	Expects: $needle, $haystack, $return_key
	Returns: corresponding item from $haystack (if found) | false (if not found)
	*/
	private function multidim_array_search( $needle, $haystack, $return_key ) {
		if ( !$haystack ) return false;
		foreach ( $haystack	as $row ) {
			if ( in_array( $needle, $row, true ) ) return $row[$return_key];	
		}
		return false;
	}
	
	/*
	Function: implode_with_escape (member of PROCESS_LINKS)
	Expects: $glue, $pieces
	Returns: same as implode (built-in function) but also deals with escape characters (for sql statements)
	*/
	private function implode_with_escape( $glue, $pieces ) {
		if ( !$pieces ) return false;
		//"'" .implode( "' OR url='", $list_of_urls ) ."'";	
		$imploded_str = ""; $iter = 0; $count_p = count( $pieces ) - 1;
		foreach ( $pieces as $piece ) {
			$piece = str_replace( "\\", "\\\\", $piece );
			$piece = str_replace( "'", "\'", $piece );
			$piece = str_replace( "\"", "\\\"", $piece );
			$imploded_str .= $piece;
			if ( $iter++ < $count_p ) $imploded_str .= $glue;
		}
		return $imploded_str;
	}
	
	/*
	End of Function Set: PROCESS_LINKS
	***************************************************************************/
	
	/*
	Function: process_page_meta
	Expects: $meta_tags
	Purpose: To process $meta_tags array from $DocInfo and save the info in the DB.
	*/
	private function process_page_meta( $meta_tags ) {
		$meta_tags_array = array();
		foreach ( $meta_tags as $key => $value ) {
			$meta_tags_array[] = "( '". $this->page_id ."', '$key', '$value' )";
		}
		
		$imploded_values = "'" .$this->implode_with_escape( "', '", $meta_tags_array ) ."'";
		$sql = "INSERT INTO cr_page_meta ( `page_id`, `key`, `value` ) VALUES $imploded_values";
		$this->mysqli->query( $sql );
	} // end of process_page_meta function
	
	/*
	Function: store_vars_in_db
	Expects: All variables referenced below should have already been saved from function run before this one.
	Purpose: To process the page info and error codes.
	*/
	private function store_vars_in_db() {
		$sql = "UPDATE `cr_pages` 
				SET `title` = '" .$this->title ."', 
					`content` = '" .$this->content ."', 
					`size` = '" .$this->file_size ."', 
					`load_time` = '." .$this->load_time ."', 
					`response_code` = '" .$this->response_code ."', 
					`doc_type` = '" .$this->doc_type ."', 
					`server_info` = '" .$this->server ."' 
				WHERE `page_id` = " .$this->page_id;
		//echo "$sql<p>";
		$this->mysqli->query( $sql );
		
		//errors
		$sql = "INSERT INTO cr_errors ( `page_id`, `recheck_crawl_id`, `error_code`, `details` ) 
				VALUES ( '" .$this->page_id ."', '0', '" .$this->error_code . "', '" .$this->error_details ."' )";
		
		//$sql statement for 
		
		
	} // end of store_vars_in_db function
	
	/*
	Function: create_sql_var
	Expects: No variables expected. Just make sure $username, $password and $db_name are all correct.
	Returns: $mysqli variable to access the DB with.
	Purpose: The $mysqli variable has been named in this class as a private variable. Therefore, it only needs to be 
			 declared once to work throughout the entire class as $this->mysqli.
	*/
	private function create_sql_var() {
		$username = "root"; $password = ""; $db_name = "crawler";
		$mysqli = new mysqli("127.0.0.1", $username, $password, $db_name);
		return $mysqli;
	}
	
	/*
	Function: sql_result_to_array
	Expects: $sql statement, $key (or column header, optional). Give a $key variable when your $sql var is returning a
			 one-dimensional array. It will be the difference between array items looking
			 like this: array( [your_key] => value )
			 vs like this: value
	Returns: $mysqli variable to access the DB with.
	Purpose: The $mysqli variable has been named in this class as a private variable. Therefore, it only needs to be 
			 declared once to work throughout the entire class as $this->mysqli.
	*/
	private function sql_result_to_array( $sql, $key = false ) {
		$result = $this->mysqli->query( $sql );
		$array = array();
		if ( !$result ) {
			return false;
		} else {
			if ( $key ) {
				while( $row_array = $result->fetch_array( MYSQLI_ASSOC ) )
					$array[] = $row_array[$key];
			} else {
				while( $row_array = $result->fetch_array( MYSQLI_ASSOC ) )
					$array[] = $row_array;	
			}
			
			$result->free();
			return $array;
		}
	}	
	
} // end of CrawlerDataStore class



?>
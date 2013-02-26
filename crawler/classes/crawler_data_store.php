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
	private $rebuilt_url, $file_size, $doc_type, $response_code, $load_time, $error_details, $error_code, $orig_urls_id,
			$server, $crawled_pages_id, $content, $title, $mysqli, $urls_id_list, $anchors_id_list, $heading_tags, $a_tags, $link_tags, 
			$img_tags, $plain_text, $prominent_keywords;
	
	/*
	Function: __construct
	Expects: $DocInfo: object created from the PHPCrawler class, containing the elements from a given url.
	Purpose: Process the $DocInfo object and save all the info in the DB.
	*/
	public function __construct( $DocInfo, $seocom_info, $crawls_id ) {
		$this->mysqli = $this->create_sql_var();
		$this->crawls_id    = $crawls_id;
		$this->a_tags       = array();
		$this->link_tags    = array();
		$this->img_tags     = array();
		$this->heading_tags = array();
		
		// first, process the $seocom_info items
		foreach ( $seocom_info as $key => $value ) {
			switch ( $key ) {
				case 'title': 				$this->title 				= $value; 	break;
				case 'server': 				$this->server 				= $value; 	break;
				case 'file_size': 			$this->file_size 			= $value; 	break;
				case 'heading_tags': 		$this->heading_tags			= $value; 	break;
				case 'a_tags': 				$this->a_tags				= $value; 	break;
				case 'link_tags': 			$this->link_tags			= $value; 	break;
				case 'img_tags': 			$this->img_tags				= $value; 	break;
				case 'plain_text':			$this->plain_text			= $value; 	break;
				case 'prominent_keywords':	$this->prominent_keywords	= $value; 	break;
				/*case 'plain_text':	$this->plain_text	= $this->mysqli->real_escape_string( 
														$value ); break;*/
			}
		}

		// next, process the link items
		$this->rebuilt_url = $DocInfo->url;
		$this->process_links( );
		// $this->process_heading_tags( );

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
				case 'meta_attributes': 	$this->process_pages_metas( $DocItem ); break; 	//array
				
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
			$cookies_list[] = "( '". $this->crawled_pages_id ."', '$key', '$value' )";
			
			//INSERT INTO `cookies` ( `crawled_pages_id`, `name` ) VALUES ( '15', 'cookie name' )	
		}
		
	} // end of process_cookies function
	

	/****************************************************************************
	FUNCTION SET: PROCESS_LINKS
	Purpose: put all link data in the database and return corresponding crawled_pages_id's
	Functions in Set: 
		1. process_links - main function of group
		2. clean_anchor
		3. get_ids
			a. get_urls_ids
			b. get_anchors_ids
			c. array_clean_unique
		4. multidim_array_search - function to search a multi-dimensional array
		5. implode_with_escape - same as implode function but also deals with escape characters
	*/
	
	/*
	Function: process_links (member of PROCESS_LINKS)
	Expects: $links_group: list of links from $DocInfo
	Purpose: Loop through the list of links ($links_group) and get urls_id's and anchors_id's for all links.
	Returns: false if $links_group is empty
	*/
	private function process_links( ) {
		if ( !$this->a_tags && !$this->link_tags && !$this->img_tags ) return false;
		
		$this->get_ids( );
		
		$list_of_a_inserts    = array( );
		$list_of_link_inserts = array( );
		$list_of_img_inserts  = array( );
		
		// process the img_tags list and create list of inserts for sql string
		// $crawled_pages_id = $this->crawled_pages_id;
		if ( $this->img_tags ) {
			$list_of_imgs_metas = array( );
			foreach ( $this->img_tags as $img_obj ) {				
				$urls_id = null;
				$raw_img_tag = null;
				$raw_src = null;

				foreach ( $img_obj as $key => $value ) {
					switch ( $key ) {
						case 'url_rebuild':
							$urls_id = $this->multidim_array_search( $value, $this->urls_id_list, 'id' );
							break;
						case 'linkcode':
							$raw_img_tag = $this->mysqli->real_escape_string( $value );
							// $raw_img_tag = $value;
							break;
						case 'src':
							$raw_src = $value;
							break;
						default:
							// replace the a_tags_id later when you have inserted the a_tag
							$list_of_imgs_metas[] = array( 	
								'raw_img_tag' 			=> $img_obj['linkcode'], 
							  	'key' 				=> $key, 
							  	'value' 			=> $value,
							  	'crawled_pages_id' 	=> $this->crawled_pages_id );
							break;
					} // end of switch statement

					$orig_crawled_pages_id = $this->orig_urls_id;
					/*****************************************************
						POSSIBLE LOCATION OF THE BUG: crawled_pages_id = 0
					******************************************************/
				} // end of foreach $img_obj loop

				if ( $urls_id > 0 ) {
					$insert_str = "( '$urls_id', '$raw_img_tag', '$raw_src', '$orig_crawled_pages_id' )";
					$list_of_img_inserts[] = $insert_str;
				}
			} // end of foreach img_tags loop

			if ( $list_of_img_inserts ) {
				$insert_values_imploded = implode( ", ", $list_of_img_inserts );
				//one sql query to insert all the img_tags texts in one shot
				$sql = "INSERT INTO cr_img_tags 
							( `urls_id`, `raw_img_tag`, `raw_src`, `crawled_pages_id` )
						VALUES $insert_values_imploded";
				$this->mysqli->query( $sql );
			}
			$sql = "SELECT `id`, `raw_img_tag` FROM `cr_img_tags`";
			$imgs_id_list = $this->sql_result_to_array( $sql );


			// use this array to complete the inserts for the meta tags.
			if ( count($list_of_imgs_metas) > 0 ) {

				$list_of_imgs_metas_inserts = array( );
				foreach ( $list_of_imgs_metas as $meta_tag ) {
					$imgs_id = $this->multidim_array_search( $meta_tag['raw_img_tag'], $imgs_id_list, 'id' );
					if ( $imgs_id > 0 ) { 
						$insert_str = "( '$imgs_id', '" 
									     .$meta_tag['key'] ."', '"
									     .$meta_tag['value'] ."', '" 
									     .$meta_tag['crawled_pages_id'] ."' )"; 
						$list_of_imgs_metas_inserts[] = $insert_str;
					}
				}
				
				unset($list_of_imgs_metas);
				if ( count($list_of_imgs_metas_inserts) > 0 ) {
					$insert_values_imploded = implode( ", ", $list_of_imgs_metas_inserts );
					//one sql query to insert all the a_tags in one shot
					$sql = "INSERT INTO cr_img_tags_metas 
								( `imgs_id`, `key`, `value`, `crawled_pages_id` ) 
							VALUES $insert_values_imploded";
					$this->mysqli->query( $sql );
				}

				// unset( $imgs_id_list ); don't unset this til after it's been used for the a_tags
				unset( $list_of_imgs_metas_inserts ); unset( $insert_values_imploded );
			}
		}

		// process the a_tags list and create list of inserts for sql string
		if ( $this->a_tags ) {
			$list_of_a_tags_metas = array( );
			foreach ( $this->a_tags as $link_obj ) {
				$imgs_id = NULL;
				/*echo "<p>______________</p>";
				print_r($link_obj);*/
				foreach ( $link_obj as $key => $value ) {
					switch ( $key ) {
						case 'anchor':
							$anchors_id = $this->multidim_array_search( $this->clean_anchor( $link_obj['anchor'] ), 
															   $this->anchors_id_list, 'id' );
							break;
						case 'url_rebuild':
							$urls_id = $this->multidim_array_search( $link_obj['url_rebuild'], $this->urls_id_list, 'id' );
							$relative = !( $link_obj['href'] == $value );
							break;
						case 'linkcode':
							$raw_a_tag = $this->mysqli->real_escape_string( $value );
							break;
						case 'href':
							$raw_href = $this->mysqli->real_escape_string( $value );
							break;
						case 'img_tag':
							// find the imgs_id for it and store it.
							$img_tag = $value;
							
							$imgs_id = $this->multidim_array_search( $img_tag, $imgs_id_list, 'id' );
							
							break;
						default:
							$list_of_a_tags_metas[] = array( 
								'a_tag' 			=> $link_obj['linkcode'], 
								'key' 	 			=> $key, 
								'value' 			=> $value,
							  	'crawled_pages_id' 	=> $this->crawled_pages_id );
							break;
					}
					
					$orig_crawled_pages_id = $this->orig_urls_id;
					/*****************************************************
						POSSIBLE LOCATION OF THE BUG: crawled_pages_id = 0
					******************************************************/
				}
				if ( $urls_id ) $list_of_a_inserts[] = 
						"( '$raw_a_tag', '$raw_href', '$urls_id', '$relative', '$anchors_id', '$imgs_id', '$orig_crawled_pages_id' )";
			}

			if ( $list_of_a_inserts ) {
				$insert_values_imploded = implode( ", ", $list_of_a_inserts );
				//one sql query to insert all the a_tags in one shot
				$sql = "INSERT INTO cr_a_tags 
							( `raw_a_tag`, `raw_href`, `urls_id`, `relative`, `anchors_id`, `imgs_id`, `crawled_pages_id` ) 
						VALUES $insert_values_imploded";
				$this->mysqli->query( $sql );
			}
		}

		// download the list of a_tags and their id's to an array
		$sql = "SELECT `id`, `raw_a_tag` FROM `cr_a_tags`";
		$a_tags_id_list = $this->sql_result_to_array( $sql );
		
		// use this array to complete the inserts for the meta tags.
		if ( $list_of_a_tags_metas ) {
			$list_of_a_tags_metas_inserts = array( );
			foreach ( $list_of_a_tags_metas as $meta_tag ) {
				$a_tags_id = $this->multidim_array_search( $meta_tag['a_tag'], $a_tags_id_list, 'id' );
				if ( $a_tags_id && !is_array( $meta_tag['value'] )  ) { 
					$list_of_a_tags_metas_inserts[] = "( '$a_tags_id', '" 
													   .$meta_tag['key'] ."', '" 
													   .$meta_tag['value'] ."', '" 
													   .$meta_tag['crawled_pages_id'] ."' )"; 
				}
			}
			unset($list_of_a_tags_metas);
			if ( $list_of_a_tags_metas_inserts ) {
				$insert_values_imploded = implode( ", ", $list_of_a_tags_metas_inserts );
				//one sql query to insert all the a_tags in one shot
				$sql = "INSERT INTO cr_a_tags_metas 
							( `a_tags_id`, `key`, `value`, `crawled_pages_id` ) 
						VALUES $insert_values_imploded";
				$this->mysqli->query( $sql );
			}

			// unset( $a_tags_id_list ); keep this for the heading_tags...
			unset( $list_of_a_tags_metas_inserts ); unset( $insert_values_imploded );
		}

		// process the link_tags list and create list of inserts for sql string
		if ( $this->link_tags ) {
			$list_of_link_tags_metas = array( );
			foreach ( $this->link_tags as $link_obj ) {
				// $crawled_pages_id = $this->crawled_pages_id;

				foreach ( $link_obj as $key => $value ) {
					switch ( $key ) {
						case 'url_rebuild':
							$urls_id = $this->multidim_array_search( $value, $this->urls_id_list, 'id' );
							$relative = !( $raw_href == $value );
							break;
						case 'linkcode':
							$raw_link_tag = $this->mysqli->real_escape_string( $value );
							break;
						case 'href':
							$raw_href = $value;
							break;
						default:
							// replace the link_tags_id later when you have inserted the a_tag
							$list_of_link_tags_metas[] = array( 
								'link_tag' 			=> $link_obj['linkcode'], 
								'key' 				=> $key,
								'value' 			=> $value, 
								'crawled_pages_id' 	=> $this->crawled_pages_id );
							break;
					}
				}
			
				if ( $urls_id ) $list_of_link_inserts[] = 
					"( '$raw_link_tag', '$raw_href', '$this->crawled_pages_id', '$urls_id' )";	
			}
			
			if ( $list_of_link_inserts ) {
				$insert_values_imploded = implode( ", ", $list_of_link_inserts );
				//one sql query to insert all the link_tags texts in one shot
				$sql = "INSERT INTO cr_link_tags 
							( `raw_link_tag`, `raw_href`, `crawled_pages_id`, `referer_crawled_pages_id` ) 
						VALUES $insert_values_imploded";
				$this->mysqli->query( $sql );
			}
		}

		// download the list of link_tags and their id's to an array
		$sql = "SELECT `id`, `raw_link_tag` FROM `cr_link_tags`";
		$link_tags_id_list = $this->sql_result_to_array( $sql );
		
		// use this array to complete the inserts for the meta tags.
		$list_of_link_tags_metas_inserts = array( );
		if ( count($list_of_link_tags_metas) > 0 ) {
			
			foreach ( $list_of_link_tags_metas as $meta_tag ) {
				$link_tags_id = $this->multidim_array_search( $meta_tag['link_tag'], $link_tags_id_list, 'id' );
				if ( $link_tags_id ) { 
					$list_of_link_tags_metas_inserts[] = "( '$link_tags_id', '" 
													   .$meta_tag['key'] ."', '" 
													   .$meta_tag['value'] ."', '"
													   .$meta_tag['crawled_pages_id'] ."' )"; 
				}
			}

			if ( count($list_of_link_tags_metas_inserts) > 0 ) {
				$insert_values_imploded = implode( ", ", $list_of_link_tags_metas_inserts );
				//one sql query to insert all the link_tags in one shot
				$sql = "INSERT INTO cr_link_tags_metas 
							( `link_tags_id`, `key`, `value`, `crawled_pages_id` ) 
						VALUES $insert_values_imploded";
				$this->mysqli->query( $sql );
			}
		}
		unset( $link_tags_id_list ); unset( $list_of_link_tags_metas_inserts ); unset( $insert_values_imploded );
		

		// process the heading_tags list and create list of inserts for sql string
		if ( count($this->heading_tags) > 0 ) {
			$list_of_heading_inserts = array( );
			$a_tags_id = NULL;
			// $crawled_pages_id = $this->crawled_pages_id;
			foreach ( $this->heading_tags as $h_obj ) {
				$level = NULL;
				$instance = NULL;
				$raw_heading_tag = NULL;
				$heading_text = NULL;
				$a_tags_id = NULL;
				
				$level = $h_obj['level'];
				$instance = $h_obj['instance'];
				$raw_heading_tag = $this->mysqli->real_escape_string( $h_obj['raw_heading_tag'] );
				$heading_text = $this->clean_anchor( $h_obj['heading_text'] );
				$a_tags_id = $this->multidim_array_search( $h_obj['raw_a_tag'], $a_tags_id_list, 'id' );
				$raw_a_tag = $h_obj['raw_a_tag'];
							
				$list_of_heading_inserts[] = 
					"( '$level', '$instance', '$this->crawled_pages_id', '$raw_heading_tag', '$a_tags_id', '$raw_a_tag', '$heading_text' )";
			}
			// print_r( $list_of_heading_inserts );
			if ( $list_of_heading_inserts ) {
				$insert_values_imploded = implode( ", ", $list_of_heading_inserts );
				//one sql query to insert all the heading_tags in one shot
				$sql = "INSERT INTO cr_heading_tags
							( `level`, `instance`, `crawled_pages_id`, `raw_heading_tag`, `a_tags_id`, `raw_a_tag`, `heading_text` ) 
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
	Purpose: Stores URLs and Anchor's in db and then populates the id's for them in $urls_id_list and $anchors_id_list
	*/
	private function get_ids( ) {
		$list_of_anchors = array();
		$list_of_urls = array();
		$list_of_urls[] = $this->rebuilt_url; //to be able to have the urls_id of the page these links are on.
		// $list_of_urls[] = $this->rebuilt_url; //to be able to have the urls_id of the page these links are on.

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
		
		//get urls_id and anchors_id for every link in $links_group
		$this->get_urls_ids( $list_of_urls );
		if ( $list_of_anchors ) $this->get_anchors_ids( $list_of_anchors );
		$this->orig_urls_id = $this->multidim_array_search( $this->rebuilt_url, $this->urls_id_list, 'id' );
		
		$sql = "INSERT INTO `cr_crawled_pages` ( `crawls_id`, `urls_id` ) 
				VALUES ( '" .$this->crawls_id ."', '" .$this->orig_urls_id ."' )";
		$this->mysqli->query( $sql );
		$this->crawled_pages_id = $this->mysqli->insert_id;

		/*
			POSSIBLE LOCATION OF THE BUG: crawled_pages_id = 0
		*/
	}
	
	/*
	Function: get_urls_ids (sub-member of PROCESS_LINKS)
	Expects: $list_of_urls: one-dim array of the urls in the $links_group
	Returns: $list_of_urls: two-dim array urls given, and the corresponding urls_id's
	*/
	private function get_urls_ids( $list_of_urls ) {
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
		//	echo $urls_imploded_insert .'<br/>';
			$sql = "INSERT INTO cr_urls ( `rebuilt_url` ) VALUES $urls_imploded_insert";
			$this->mysqli->query( $sql );
			// echo "<p>$sql</p>";
		}  // end of if statement
		$sql = "SELECT id, rebuilt_url FROM `cr_urls` WHERE $urls_imploded_select";
		$this->urls_id_list = $this->sql_result_to_array( $sql );
	}
	
	/*
	Function: get_anchors_ids (sub-member of PROCESS_LINKS)
	Expects: $list_of_anchors: one-dim array of the anchor texts in the $links_group
	Returns: $list_of_anchors: two-dim array anchor texts given, and the corresponding anchorss_id's
	*/
	private function get_anchors_ids( $list_of_anchors ) {
		$list_of_anchors = $this->array_clean_unique( $list_of_anchors );
		$anchors_imploded_select = "trimmed_anchor='" 
			.$this->implode_with_escape( "' OR trimmed_anchor='", $list_of_anchors ) ."'";
		//looks like this: trimmed_anchor='seo company' OR trimmed_anchor='seo' OR trimmed_anchor='etc'
		
		//query the db and get the rows
		$sql = "SELECT trimmed_anchor FROM `cr_anchors` WHERE $anchors_imploded_select";
		$anchors_in_db = $this->sql_result_to_array( $sql, 'trimmed_anchor' );
		
		$anchors_id_array = false;
		if ( count( $anchors_in_db ) != count( $list_of_anchors ) ) {
			//check which URLs are not in the db
			$anchors_not_in_db = array_diff( $list_of_anchors, $anchors_in_db );
			
			//compile insert stmt containing the $urls_not_in_db
			$anchors_imploded_insert = "( '" .$this->implode_with_escape( "' ), ( '", $anchors_not_in_db ) ."' )";
			
			$sql = "INSERT INTO cr_anchors ( trimmed_anchor ) VALUES $anchors_imploded_insert";
			$this->mysqli->query( $sql );
			
			$sql = "SELECT trimmed_anchor, id FROM `cr_anchors` WHERE $anchors_imploded_select";
			$this->anchors_id_list = $this->sql_result_to_array( $sql );
		} // end of if statement
	} // end of get_anchors_ids function
	
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
		// echo $implode_str . '<br/>';
		return $imploded_str;
	}
	
	/*
	End of Function Set: PROCESS_LINKS
	***************************************************************************/
	
	/*
	Function: process_pages_metas
	Expects: $meta_tags
	Purpose: To process $meta_tags array from $DocInfo and save the info in the DB.
	*/
	private function process_pages_metas( $meta_tags ) {
		$meta_tags_array = array();

		foreach ( $meta_tags as $key => $value ) {
			$meta_tags_array[] = "( '". $this->crawled_pages_id ."', '$key', '$value' )";
		}
		
		$imploded_values = implode( ", ", $meta_tags_array );
		$sql = "INSERT INTO cr_crawled_pages_metas ( `crawled_pages_id`, `key`, `value` ) VALUES $imploded_values";
		$this->mysqli->query( $sql );
	} // end of process_pages_metas function
	
	/*
	Function: store_vars_in_db
	Expects: All variables referenced below should have already been saved from function run before this one.
	Purpose: To process the page info and error codes.
	*/
	private function store_vars_in_db() {
		$sql = "UPDATE `cr_crawled_pages` 
				SET `title` = '" .$this->title ."', 
					`content` = '" .$this->content ."', 
					`size` = '" .$this->file_size ."', 
					`load_time` = '." .$this->load_time ."', 
					`response_code` = '" .$this->response_code ."', 
					`doc_type` = '" .$this->doc_type ."', 
					`server_info` = '" .$this->server ."' 
				WHERE `id` = " .$this->crawled_pages_id;
		
		$this->mysqli->query( $sql );

		$sql = "INSERT INTO cr_prominent_keywords ( crawled_pages_id, plain_text, prominent_keywords_array ) 
				VALUES ( '$this->crawled_pages_id', '$this->plain_text', '$this->prominent_keywords' )";
		// echo "<p>$sql</p>";
		$this->mysqli->query( $sql );
		
		



		//errors
		$sql = "INSERT INTO cr_errors ( `crawled_pages_id`, `recheck_crawls_id`, `error_code`, `details` ) 
				VALUES ( '" .$this->crawled_pages_id ."', '0', '" .$this->error_code . "', '" .$this->error_details ."' )";
		
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
		$username = "seotools"; $password = "brokenlinks#3"; $db_name = "crawler";
		$mysqli = new mysqli("127.0.0.1", $username, $password, $db_name);
		return $mysqli;
	}

	
	/*
	Function: sql_result_to_array
	Expects: $sql statement, $key (or column heading, optional). Give a $key variable when your $sql var is returning a
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
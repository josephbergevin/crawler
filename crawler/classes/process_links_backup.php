<?php

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
	private function process_links( $links_group ) {
		if ( !$links_group ) return false;
		
		$this->get_ids( $links_group );
		
		$list_of_a_inserts = false;
		$list_of_link_inserts = false;
		
		foreach ( $links_group as $link_obj ) {
			//process the anchor text link: Add it to a string that will be inserted all at once after this loop	
			//echo "<p>" .$link_obj['linkcode'] ."</p>";
			if ( $link_obj['linkcode'] ) {	
				
				if ( substr( $link_obj['linkcode'], 0, 3 ) == "<a " ) {
					//echo "<p>1st 3 char: |" .substr( $link_obj['linkcode'], 0, 3 ) ."|</p>";
					$anchor_id = $this->multidim_array_search( $this->clean_anchor( $link_obj['linktext'] ), 
															   $this->anchor_id_list, 'anchor_id' );
					$url_id = $this->multidim_array_search( $link_obj['url_rebuild'], $this->url_id_list, 'url_id' );
					$raw_a_tag = $this->mysqli->real_escape_string( $link_obj['linkcode'] );
					$raw_href = $this->mysqli->real_escape_string( $link_obj['link_raw'] );
					$relative = !( $link_obj['link_raw'] == $link_obj['url_rebuild'] );
					$img_id = NULL;
					$page_id = $this->orig_url_id;
					
					if ( $url_id ) $list_of_a_inserts[] = 
						"( '$url_id', '$raw_href', '$relative', '$anchor_id', '$raw_a_tag', '$img_id', '$page_id' )";
				} // end of 'if ( substr' statement
				
				if ( substr( $link_obj['linkcode'], 0, 6 ) == "<link " ) {
					//echo "<p>1st 6 char: |" .substr( $link_obj['linkcode'], 0, 6 ) ."|</p>";
					$anchor_id = $this->multidim_array_search( $this->clean_anchor( $link_obj['linktext'] ), 
															   $this->anchor_id_list, 'anchor_id' );
					$url_id = $this->multidim_array_search( $link_obj['url_rebuild'], $this->url_id_list, 'url_id' );
					$raw_link_tag = $this->mysqli->real_escape_string( $link_obj['linkcode'] );
					$raw_href = $link_obj['link_raw'];
					$relative = !( $raw_href == $link_obj['url_rebuild'] );
					$img_id = NULL;
					$page_id = $this->page_id;
					
					if ( $url_id ) $list_of_link_inserts[] = 
						"( '$raw_link_tag', '$page_id', '$url_id' )";	
				}
			}
		}
		
		if ( $list_of_a_inserts ) {
			$insert_values_imploded = implode( ", ", $list_of_a_inserts );
			//one sql query to insert all the anchor texts in one shot
			$sql = "INSERT INTO a_tags 
						( `url_id`, `raw_href`, `relative`, `anchor_id`, `raw_anchor`, `img_id`, `page_id` ) 
					VALUES $insert_values_imploded";
			//echo "<p>$sql<p>";
			$this->mysqli->query( $sql );
		}
		
		if ( $list_of_link_inserts ) {
			$insert_values_imploded = implode( ", ", $list_of_link_inserts );
			//one sql query to insert all the anchor texts in one shot
			$sql = "INSERT INTO link_tags 
						( `raw_link_tag`, `page_id`, `url_id` ) 
					VALUES $insert_values_imploded";
			//echo "<p>$sql<p>";
			$this->mysqli->query( $sql );
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
	private function get_ids( $links_group ) {
		$list_of_anchors = array();
		$list_of_urls = array();
		$list_of_urls[] = $this->rebuilt_url; //to be able to have the url_id of the page these links are on.
		
		foreach ( $links_group as $link_obj ) {
			if ( $link_obj['linkcode'] ) {
				if ( substr( $link_obj['linkcode'], 0, 3 ) == "<a " OR substr( $link_obj['linkcode'], 0, 6 ) == "<link " ) { 
					$list_of_urls[] = $link_obj['url_rebuild'];
					$list_of_anchors[] = $this->clean_anchor( $link_obj['linktext'] );
				}
			}
		}
		
		$list_of_urls = $this->array_clean_unique( $list_of_urls );
		if ( $list_of_anchors ) $list_of_anchors = $this->array_clean_unique( $list_of_anchors );
		
		//get url_id and anchor_id for every link in $links_group
		$this->get_url_ids( $list_of_urls );
		if ( $list_of_anchors ) $this->get_anchor_ids( $list_of_anchors );
		$this->orig_url_id = $this->multidim_array_search( $this->rebuilt_url, $this->url_id_list, 'url_id' );
		$sql = "INSERT INTO `pages` ( `crawl_id`, `url_id` ) 
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
		$sql = "SELECT rebuilt_url FROM `urls` WHERE $urls_imploded_select";
		$urls_in_db = $this->sql_result_to_array( $sql, 'rebuilt_url' );
		
		if ( count( $urls_in_db ) != count( $list_of_urls ) ) { //if there are URLs that are not already in the DB
			//check which URLs are not in the db
			$urls_not_in_db = array_diff( $list_of_urls, $urls_in_db );
			
			//compile insert stmt containing the $urls_not_in_db
			$urls_imploded_insert = "( '" .$this->implode_with_escape( "' ), ( '", $urls_not_in_db ) ."' )";
			
			$sql = "INSERT INTO urls ( `rebuilt_url` ) VALUES $urls_imploded_insert";
			$this->mysqli->query( $sql );
		}  // end of if statement
		$sql = "SELECT url_id, rebuilt_url FROM `urls` WHERE $urls_imploded_select";
		$this->url_id_list = $this->sql_result_to_array( $sql );
		//echo "<p>$sql</p>";
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
		$sql = "SELECT trimmed_anchor FROM `anchors` WHERE $anchors_imploded_select";
		$anchors_in_db = $this->sql_result_to_array( $sql, 'trimmed_anchor' );
		
		$anchor_id_array = false;
		if ( count( $anchors_in_db ) != count( $list_of_anchors ) ) {
			//check which URLs are not in the db
			$anchors_not_in_db = array_diff( $list_of_anchors, $anchors_in_db );
			
			//compile insert stmt containing the $urls_not_in_db
			$anchors_imploded_insert = "( '" .$this->implode_with_escape( "' ), ( '", $anchors_not_in_db ) ."' )";
			
			$sql = "INSERT INTO anchors ( trimmed_anchor ) VALUES $anchors_imploded_insert";
			$this->mysqli->query( $sql );
			
			$sql = "SELECT trimmed_anchor, anchor_id FROM `anchors` WHERE $anchors_imploded_select";
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

	?>
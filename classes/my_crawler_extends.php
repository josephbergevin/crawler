<?php

include_once( "crawler_data_store.php" );
// Inculde the phpcrawl-mainclass
include("PHPCrawl_080/libs/PHPCrawler.class.php");
include("PHPCrawl_080/libs/PHPCrawlerUtils.class.php");
include_once('simplehtmldom_1_5/simple_html_dom.php');


class MyCrawler extends PHPCrawler {
	public $seocom_info, $a_tags_from_headings, $root_url;
	
	function handleDocumentInfo( PHPCrawlerDocumentInfo $DocInfo ) {
		$time_start = microtime( true );
		$this->root_url = $DocInfo->protocol .$DocInfo->host;
		$dom = str_get_html( $DocInfo->content );
		$this->seocom_info = array( );
		$this->seocom_info['title'] = $this->parse_title( $dom );
		$this->seocom_info['headings'] = $this->parse_headings( $dom );
		$this->parse_links( $DocInfo->links_found );
		$responseHeader = $DocInfo->responseHeader;
		if ( $responseHeader ) {
			foreach ( $responseHeader as $key => $value ) {
				switch ( $key ) {
					case 'server':
						$this->seocom_info['server'] = $responseHeader->server;
						break;
					case 'content_length':
						$this->seocom_info['file_size'] = $responseHeader->content_length;
						break;
				}
			}
		}
		/* THE OLD CODE:
		if ( $responseHeader->server ) $this->seocom_info['server'] = $responseHeader->server;
		if ( $responseHeader->content_length )
			$this->seocom_info['file_size'] = $responseHeader->content_length;
		*/
		$time_end = microtime( true );
		$total_time = $time_end - $time_start;
		$this->seocom_info['processing_time'] = $total_time;

		//print_r( $this->seocom_info );
		$page_to_scrape = new CrawlerDataStore( $DocInfo, $this->seocom_info );
		
		
	}
	
	/*
	Function: parse_title
	Expects: $dom (SimpleDom object)
	Purpose: Get the title from the given html.
	Returns: Title.
	*/
	public static function parse_title( $dom ) {                
		/*if ( preg_match( "/<title>(.+)<\/title>/i", $source, $matches ) )
     		return $matches[1];*/
     	if ( !$dom ) return false;
     	$object_group = $dom->find('title');
		foreach ( $object_group as $object )
			if ( $object->innertext() != "" ) return $object->innertext();
		return false;
	}

	/*
	Function: parse_headings
	Expects: $dom (SimpleDom object)
	Purpose: Return array with heading tag information (h1 - h6) for the given html ($source).
	Returns: Array of Heading Info.
	*/
	public function parse_headings( $dom ) {
		if ( !$dom ) return false;
		$headings_array = array( );
		$this->a_tags_from_headings = array( );
		for ( $h_level = 1; $h_level <= 6; $h_level++ ) {
			$heading_group = $dom->find( "h$h_level" );
			$h_instance = 0;
			foreach ( $heading_group as $heading_tag ) {
				$h_instance++;
				$a_tags = $heading_tag->find( "a" );
				$a_tag_raw = null; //return false for the a_tag_raw var if no a tag exists in the header_raw code
				if ( $a_tags ) {
					$a_tag_raw = $a_tags[0]->outertext;
					$this->a_tags_from_headings[] = $a_tag_raw;
					/*foreach ( $a_tags as $a_tag )
						$a_tag_raw = $a_tag->outertext;*/
				}
				$headings_array[] = array( 'level' => $h_level, 
										   'instance' => $h_instance,
										   'header_text' => $heading_tag->plaintext, 
										   'raw_header' => $heading_tag->outertext,
										   'a_tag_raw' => $a_tag_raw );
			}
		}
		// print_r( $headings_array );
		return $headings_array;
	}

	/*
	Function: parse_links
	Expects: $dom (SimpleDom object)
	Purpose: Return array with imgs for the given html ($source).
	Returns: Array of imgs and the corresponding meta info.
	*/
	public function parse_links( $links_group ) {
		$a_tag_array = array( );
		$img_tag_array = array( );
		$link_tag_array = array( );

		// print_r( $links_group );

		// this foreach is used to add the a_tags that were found within heading tags to the list of a_tag links.			
		foreach ( $this->a_tags_from_headings as $a_tag ) {
			$dom = str_get_html( $a_tag );
			$link_array_item = array();
			$link_array_item['linkcode'] = $a_tag;

			$link_array_item['url_rebuild'] = $this->url_rebuilder( $dom->root->children[0]->attr['href'], $this->root_url );
			// echo "<p>" .$dom->root->children[0]->attr['href'] ."</p>";
			// echo $link_array_item['url_rebuild'];
			$link_array_item['anchor'] = $dom->plaintext;
			
			// get the attributes of the link
			foreach ( $dom->root->children[0]->attr as $key => $value )
				$link_array_item[$key] = $value;
			
			// process the children, if any...
			$children_group = $dom->root->children[0]->children;
			if ( $children_group ) {
				$link_array_children_item = array();
				foreach ( $children_group as $obj_item )
					$link_array_children_item[] = $obj_item->outertext;
				$link_array_item['children'] = $link_array_children_item;
				$link_array_children_item = null;
			}
			$a_tag_array[] = $link_array_item;
		}

		foreach ( $links_group as $link_obj ) {
			//process the anchor text link: Add it to a string that will be inserted all at once after this loop	
			if ( $link_obj['linkcode'] ) {	
				if ( substr( $link_obj['linkcode'], 0, 3 ) == "<a " ) {
					//save it in the a_tags array (along with all of it's attributes)
					$link_array_item = array();
					$link_array_item['linkcode'] = $link_obj['linkcode'];
					$link_array_item['url_rebuild'] = $link_obj['url_rebuild'];
					$dom = str_get_html( $link_obj['linkcode'] );
					$link_array_item['anchor'] = $dom->plaintext;
					
					// get the attributes of the link
					foreach ( $dom->root->children[0]->attr as $key => $value )
						$link_array_item[$key] = $value;
					
					// process the children, if any...
					$children_group = $dom->root->children[0]->children;
					if ( $children_group ) {
						$link_array_children_item = array();
						foreach ( $children_group as $obj_item )
							$link_array_children_item[] = $obj_item->outertext;
						$link_array_item['children'] = $link_array_children_item;
						$link_array_children_item = null;
					}
					$a_tag_array[] = $link_array_item;
					
					
				} // end of 'if ( substr' statement
				
				if ( substr( $link_obj['linkcode'], 0, 6 ) == "<link " ) {
					//save it in the link_tags array (along with all of it's attributes)
					$link_array_item = array();
					$link_array_item['linkcode'] = $link_obj['linkcode'];
					$link_array_item['url_rebuild'] = $link_obj['url_rebuild'];
					$dom = str_get_html( $link_obj['linkcode'] );
					
					// get the attributes of the link
					foreach ( $dom->root->children[0]->attr as $key => $value )
						$link_array_item[$key] = $value;
					
					$children_group = $dom->root->children[0]->children;
					if ( $children_group ) {
						$link_array_children_item = array();
						foreach ( $children_group as $obj_item )
							$link_array_children_item[] = $obj_item->outertext;
						$link_array_item['children'] = $link_array_children_item;
						$link_array_children_item = null;
					}
					$link_tag_array[] = $link_array_item;
				} // end of if substr statement

				if ( substr( $link_obj['linkcode'], 0, 5 ) == "<img " ) {
					//save it in the img_tags array (along with all of it's attributes)
					$img_array_item = array();
					$img_array_item['linkcode'] = $link_obj['linkcode'];
					$img_array_item['url_rebuild'] = $link_obj['url_rebuild'];
					$dom = str_get_html( $link_obj['linkcode'] );
					
					// get the attributes of the link
					foreach ( $dom->root->children[0]->attr as $key => $value )
						$img_array_item[$key] = $value;
					
					$children_group = $dom->root->children[0]->children;
					if ( $children_group ) {
						$img_array_children_item = array();
						foreach ( $children_group as $obj_item )
							$img_array_children_item[] = $obj_item->outertext;
						$img_array_item['children'] = $img_array_children_item;
						$img_array_children_item = null;
					}
					$img_tag_array[] = $img_array_item;
				} // end of if substr statement

				$meta_group = null; $link_array_item = null;
			} // end of if $link_obj statement
		} // end of foreach $links_group loop
		if ( $a_tag_array ) $this->seocom_info['a_tags'] = $a_tag_array;
		if ( $link_tag_array ) $this->seocom_info['link_tags'] = $link_tag_array;
		if ( $img_tag_array ) $this->seocom_info['img_tags'] = $img_tag_array;

		// print_r( $a_tag_array );
		// $img_tag_array;
		// $link_tag_array
	}

	public function url_rebuilder( $url_to_check, $root_url ) {
		if ( substr( $url_to_check, 0, 4 ) == 'http' ) return $url_to_check;
		if ( substr( $url_to_check, 0, 1 ) == '/' ) return $root_url .$url_to_check;
		if ( substr( $url_to_check, 0, 1 ) !== '/' ) return "$root_url/$url_to_check";
		if ( substr( $url_to_check, 0, 1 ) !== './' ) 
			return $root_url .substr( $url_to_check, 1, strlen( $url_to_check ) - 1 );

		return $url_to_check;
	}

	/*
	Function: process_redirect
	Expects: $rebuilt_url
	Purpose: Find the redirect path from the $response_headers array
	Returns: Array containing redirect info.
	*/
	//****NEEDS TO BE ADJUSTED FOR NEW CRAWLER
	/*private function process_redirect( $url ) {
		$response_headers = get_headers( $url )
		// $redirect_from_url_id = $this->url_id;
		// $redirect_status_code_id = $this->status_code_id;
		foreach ( $response_headers as $response_item ) {
			if (strtolower( substr( $response_item, 0, strpos( $response_item, ": " ) ) ) == 'location' ) {
				$redirect_url = substr( $response_item, 2 + strpos( $response_item , ": " ) );
				//echo "<p>$redirect_url | ";
				$redirect_to_url_id = $this->get_redirect_url_id( $redirect_url );
				$redirect_to_url_id = $redirect_to_url_id[0];
				//echo "$redirect_to_url_id<p>";
				$sql = "UPDATE url_info SET redirect_url_id = '$redirect_to_url_id', status_code_id = '$redirect_status_code_id'
						WHERE url_id = $redirect_from_url_id";
				mysql_query( $sql );
				$redirect_from_url_id = $redirect_to_url_id;
				$redirect_status_code_id = 0;
				//$this_link = new Link( $link_obj, $orig_url_id, $orig_url );
				
			}
			
			if (strtolower( substr( $response_item, 0, strpos( $response_item, "/" ) ) ) == 'http' )
				$redirect_status_code_id = substr( $response_item, 1 + strpos( $response_item, " " ), 3 );
			
		}
		
		return;
		//check the SQL DB for the $redirect_url and then return the url_id for it
		$redirect_sql_row = $this->find_href_in_sql( $redirect_url );
		
		//if it doesn't exist, add it to the list and return the url_id after adding it
		if ( !$redirect_sql_row ) {
			$sql = "INSERT INTO url_info ( url ) VALUES ('$redirect_url')";
			mysql_query( $sql );
			$redirect_sql_row = mysql_insert_id();
		}
		
	} */// end of process_redirect function
	
}



?>

<?php


?>
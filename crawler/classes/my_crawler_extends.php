<?php

require_once( "crawler_data_store.php" );

// Inculde the phpcrawl-mainclass
require_once("PHPCrawl_080/libs/PHPCrawler.class.php");
require_once("PHPCrawl_080/libs/PHPCrawlerUtils.class.php");
require_once("ScraperMoreFaster.php");
require_once("pkw_class.php");

class MyCrawler extends PHPCrawler {
	public $seocom_info;
	public $a_tags_from_heading_tags;
	public $root_url;
	public $scrapermorefaster;
	public $scrapermorefaster_small;
	public $crawls_id;
	
	function handleDocumentInfo( PHPCrawlerDocumentInfo $DocInfo ) {
		$time_start = microtime( true );

		$this->scrapermorefaster 		= New ScraperMoreFaster;
		$this->scrapermorefaster_small 	= New ScraperMoreFaster;
		$this->scrapermorefaster->str_get_html( $DocInfo->content );
		$this->root_url = $DocInfo->protocol .$DocInfo->host;
		
		$this->seocom_info = array( );
		$this->seocom_info['title'] = $this->scrapermorefaster->getTitleDom();
		$this->seocom_info['heading_tags'] = $this->scrapermorefaster->getHeadingTagsDom();
		$this->seocom_info['plain_text'] = $this->scrapermorefaster->plaintext();
		$this->seocom_info['prominent_keywords'] = serialize($this->getPKWs());

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

		$time_end = microtime( true );
		$total_time = $time_end - $time_start;
		$this->seocom_info['processing_time'] = $total_time;

		
		$page_to_scrape = new CrawlerDataStore( $DocInfo, $this->seocom_info, $this->crawls_id );
		
		
	}
	
	/*
	Function: parse_title
	Expects: $dom (SimpleDom object)
	Purpose: Get the title from the given html.
	Returns: Title.
	Note: This function has been deprecated with the use of class: ScraperMoreFaster
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
	Function: parse_heading_tags
	Expects: $dom (SimpleDom object)
	Purpose: Return array with heading tag information (h1 - h6) for the given html ($source).
	Returns: Array of Heading Info.
	Note: This function has been deprecated with the use of class: ScraperMoreFaster
	*/
	public function parse_heading_tags() {
		if ( !$dom ) return false;
		$heading_tags_array = array( );
		$this->a_tags_from_heading_tags = array( );
		for ( $h_level = 1; $h_level <= 6; $h_level++ ) {
			$heading_group = $dom->find( "h$h_level" );
			$h_instance = 0;
			foreach ( $heading_group as $heading_tag ) {
				$h_instance++;
				$a_tags = $heading_tag->find( "a" );
				$a_tag_raw = null; //return false for the a_tag_raw var if no a tag exists in the header_raw code
				if ( $a_tags ) {
					$a_tag_raw = $a_tags[0]->outertext;
					$this->a_tags_from_heading_tags[] = $a_tag_raw;
					/*foreach ( $a_tags as $a_tag )
						$a_tag_raw = $a_tag->outertext;*/
				}
				$heading_tags_array[] = array( 
					'level' 			=> $h_level, 
					'instance' 			=> $h_instance,
					'heading_text' 		=> $heading_tag->plaintext, 
					'raw_heading_tag' 	=> $heading_tag->outertext,
					'a_tag_raw' 		=> $a_tag_raw );
			}
		}
		// print_r( $heading_tags_array );
		return $heading_tags_array;
	}

	/*
	Function: getPKWs
	Purpose: Use this type of naming structure for a function that creates something.
	*/
	public function getPKWs() {
		$pkw = New ProminentKeywords;
		$pkw->__set('kw_combo_max', 3);
		$pkw->__set('content', strtolower($this->seocom_info['plain_text']));
		return $pkw->return_result();
	} // end of getPKWs function

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

		if(isset($this->a_tags_from_heading_tags)){
			// this foreach is used to add the a_tags that were found within heading tags to the list of a_tag links.			
			foreach ( $this->a_tags_from_heading_tags as $a_tag ) {
				
				$link_array_item = array();
				$link_array_item['linkcode'] = $a_tag;
				
				$this->scrapermorefaster_small->str_get_html( $a_tag );
				$link_array_item['anchor'] = $this->scrapermorefaster_small->plaintext();
				$a_tag_href = $this->scrapermorefaster_small->getMetaData();

				$link_array_item['url_rebuild'] = $this->url_rebuilder( $a_tag_href['href'], $this->root_url );
				
				$this->scrapermorefaster_small->
					str_get_html( $link_array_item['url_rebuild'] );
				$link_array_item['anchor'] = $this->scrapermorefaster_small->plaintext();
				
				// get the attributes of the link
				$attributes = $this->scrapermorefaster_small->getMetaTagAttributes('a', null, true);
				foreach ( $attributes as $key => $value ) {
					$link_array_item[$key] = $value;
				}

				$a_tag_array[] = $link_array_item;
			}
		}
		foreach ( $links_group as $link_obj ) {
			//process the anchor text link: Add it to a string that will be inserted all at once after this loop	
			if ( $link_obj['linkcode'] ) {	
				if ( substr( $link_obj['linkcode'], 0, 3 ) == "<a " ) {
					//save it in the a_tags array (along with all of it's attributes)
					$link_array_item = array();
					$link_array_item['linkcode'] = $link_obj['linkcode'];
					$link_array_item['url_rebuild'] = $link_obj['url_rebuild'];
					$link_array_item['anchor'] = $link_obj['linktext'];

					$this->scrapermorefaster_small->
						str_get_html( $link_array_item['linkcode'] );
					


					// get the attributes of the link
					$attributes = $this->scrapermorefaster_small->getMetaTagAttributes('a', null, true);
					foreach ( $attributes as $key => $value ) {
						$link_array_item[$key] = $value;
					}
					/*echo "<p>_____link_array_item_____</p><pre>";
					print_r($link_array_item);
					echo "</pre>";*/

					$a_tag_array[] = $link_array_item;
					
					
				} // end of 'if ( substr' statement
				
				if ( substr( $link_obj['linkcode'], 0, 6 ) == "<link " ) {
					//save it in the link_tags array (along with all of it's attributes)
					$link_array_item = array();
					$link_array_item['linkcode'] = $link_obj['linkcode'];
					$link_array_item['url_rebuild'] = $link_obj['url_rebuild'];
					
					$this->scrapermorefaster_small->
						str_get_html( $link_array_item['linkcode'] );

					// get the attributes of the link
					$attributes = $this->scrapermorefaster_small->getMetaTagAttributes('link', null, false);
					foreach ( $attributes as $key => $value ) {
						$link_array_item[$key] = $value;
					}

					$link_tag_array[] = $link_array_item;
				} // end of if substr statement

				if ( substr( $link_obj['linkcode'], 0, 5 ) == "<img " ) {
					//save it in the img_tags array (along with all of it's attributes)
					$link_array_item = array();
					$link_array_item['linkcode'] = $link_obj['linkcode'];
					$link_array_item['url_rebuild'] = $link_obj['url_rebuild'];
					
					$this->scrapermorefaster_small->str_get_html( $link_obj['linkcode'] );
					
					// get the attributes of the link
					$attributes = $this->scrapermorefaster_small->getMetaTagAttributes('img', null, false);
					foreach ( $attributes as $key => $value ) {
						$link_array_item[$key] = $value;
					}
					/*echo "<p>__________</p>";
					print_r($link_array_item);*/

					$img_tag_array[] = $link_array_item;
				} // end of if substr statement

				$meta_group = null; $link_array_item = null;
			} // end of if $link_obj statement
		} // end of foreach $links_group loop
		if ( $a_tag_array ) $this->seocom_info['a_tags'] = $a_tag_array;
		if ( $link_tag_array ) $this->seocom_info['link_tags'] = $link_tag_array;
		if ( $img_tag_array ) $this->seocom_info['img_tags'] = $img_tag_array;
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
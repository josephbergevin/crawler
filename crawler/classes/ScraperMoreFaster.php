<?php

/*
Class: ScraperMoreFaster
Author: Joe Bergevin (joe@joescode.com)
Purpose: 
	
Call Examples: 	
	
*/

class ScraperMoreFaster {

	public $html = false;
	protected $domDoc = false;
	protected $responseHeadings = false;

	/*
	Function: file_get_html
	Expects: $url to get the HTML contents.
	Purpose: Retrieve the HTML contents from the given.
	*/
	public function file_get_html($url)
	{
		$this->html = file_get_contents($url);
		$this->responseHeadings = $http_response_heading;
		return;
	} // end of file_get_html function

	/*
	Function: str_get_html
	Expects: String containing HTML code.
	Purpose: Retrieve the HTML contents from the given 
	*/
	public function str_get_html($html_str)
	{
		$this->html = $html_str;
		return;
	} // end of str_get_html function

	/*
	Function: plaintext
	Expects: $html var must be filled first.
	Purpose: Parse the plain text (visible text) from an HTML document.
	Returns: Plain Text of an HTML document.
	*/
	public function plaintext( $html_str = null )
	{
		// remove comments and any content found in the the comment area (strip_tags only removes the actual tags).
		if ( !$html_str ) {
			$plaintext = preg_replace('#<!--.*?-->#s', '', $this->html);
		} else {
			$plaintext = preg_replace('#<!--.*?-->#s', '', $html_str); // for use of this function within this class
		}

		$plaintext = html_entity_decode($plaintext);

		// put a space between list items (strip_tags just removes the tags).
		$plaintext = preg_replace('#</li>#', ' </li>', $plaintext);
		
		// remove all script and style tags
		$plaintext = preg_replace('#<(script|style)\b[^>]*>(.*?)</(script|style)>#is', "", $plaintext);
		
		// remove br tags (missed by strip_tags)
		$plaintext = preg_replace("#<br[^>]*?>#", " ", $plaintext);
		
		// remove all remaining html
		$plaintext = strip_tags($plaintext);
		$plaintext = preg_replace('#\s+#', ' ', $plaintext);
		$plaintext = htmlspecialchars_decode($plaintext, ENT_QUOTES);
		$plaintext = preg_replace('/&(#\d+|\w+);/', ' ', $plaintext);
		
		return $plaintext;
	} // end of plaintext function

	/*
	Function: getElementById
	Expects: $html var must be filled first.
	Purpose: Retrieve the HTML contents from the given 
	Returns: Plain Text of an HTML document.
	*/
	public function getElementById( $elementId )
	{
		$this->loadDom();
		return $this->domDoc->getElementById($elementId);
	} // end of getElementById function

	/*
	Function: getElementByName
	Expects: $html var must be filled first.
	Purpose: Retrieve the HTML contents from the given 
	Returns: Plain Text of an HTML document.
	*/
	public function getElementByName( $elementName )
	{
		$this->loadDom();
		return $this->domDoc->getElementByName($elementName);
	} // end of getElementByName function

	/*
	Function: loadDom
	Purpose: Load the HTML document into the DomDocument class 
	*/
	public function loadDom( $overwrite = false )
	{
		if ( !$this->domDoc ) {
			$this->domDoc = new DomDocument;
			if ( isset($this->html) ) {
				try{
					$this->domDoc->loadHTML($this->html);
				}catch(Exception $e){

				}
			}
		} else {
			if ( $overwrite ) {
				$this->domDoc = new DomDocument;
				try{
					$this->domDoc->loadHTML($this->html);
				}catch(Exception $e){

				}
			}
		}
	} // end of loadDom function

	/*
	Function: 	getTitleDom
	Purpose: 	Gets the title from the source stored in the $html variable.
	Returns: 	Title as string.
	*/
	public function getTitleDom()
	{
		$this->loadDom();
		$title = null;
		
		$found = $this->domDoc->getElementsByTagName("title");
		if ( $found->length > 0 ) {
			$title = $found->item(0)->nodeValue;
		}
		
		return $title;
	}

  	/*
	Function: 	getHeadingTagsDom
	Purpose: 	Gets all heading tags from the source stored in the $html variable.
	Returns: 	Associative array conatining all found heading tags.
	*/
	public function getHeadingTagsDom()
	{                
  		$this->loadDom();      

		$headings = array();
		for ($type = 1; $type < 6; $type++) {
			$matches = $this->domDoc->getElementsByTagName("h$type");
			$h_instance = 1;
			foreach ($matches as $h) {
				$raw_heading_tag = $this->domDoc->saveHTML($h);
				$raw_a_tag = $this->getATag($raw_heading_tag);
				$headings[] = array(
					'level' 			=> $type, 
					'instance' 			=> $h_instance++,
					'heading_text' 		=> trim($h->nodeValue),
					'raw_heading_text' 	=> $this->innerHTML($h),
					'raw_heading_tag' 	=> $raw_heading_tag,
					'raw_a_tag' 		=> $raw_a_tag
				);
			}
		}

		return $headings;
	}

	/*
	Function: 	innerHTMLDom
	Purpose: 	Use this when you want the entire nodeValue with HTML code
	Returns: 	the nodeValue with including HTML (if it has any).
	*/
	public function innerHTMLDom() 
	{  
		return $this->domDoc->saveXML();
	}

	/*
	Function: 	innerHTML
	Purpose: 	Use this when you want the entire nodeValue with HTML code
	Returns: 	the nodeValue with including HTML (if it has any).
	*/
	public function innerHTML( $node ) 
	{ 
		$doc = $node->ownerDocument;
		$frag = $doc->createDocumentFragment();
		
		foreach ($node->childNodes as $child) { 
			$frag->appendChild($child->cloneNode(TRUE)); 
		} 
		return $doc->saveXML($frag);
	}

	/*
	Function: 	getMetaData
	Purpose: 	Gets all the meta data from the given html source.
	Returns: 	Associative array conatining all found meta attributes.
	*/
	public function getMetaData( $html_str = null )
	{                
  		if ( !$html_str ) {
			
			preg_match_all(	"#\s(\w*)\s*=\s*(?|\"([^\"]+)\"|'([^']+)'|([^\s><'\"]+))#i", 
							$this->html, 
							$matches
						  );
		} else {
			preg_match_all(	"#\s(\w*)\s*=\s*(?|\"([^\"]+)\"|'([^']+)'|([^\s><'\"]+))#i", 
							$html_str, 
							$matches
						  );
		}
		$meta_data = array_combine($matches[1], $matches[2]);
		return $meta_data;
	}

	/*
	Function: 	getMetaRegEx
	Purpose: 	Gets all meta-tag attributes from the source stored in the $html variable.
	Returns: 	Associative array conatining all found meta-attributes.
				The keys are the meta-names, the values the content of the attributes.
				(like $tags["robots"] = "nofollow")
	Note: 		Uses the same regex statement as used in the PHPCrawl class, written by Uwe Hunfeld
	*/
	public function getMetaRegEx()
	{
		preg_match_all(	"#<\s*meta\s+".
						"name\s*=\s*(?|\"([^\"]+)\"|'([^']+)'|([^\s><'\"]+))\s+".
						"content\s*=\s*(?|\"([^\"]+)\"|'([^']+)'|([^\s><'\"]+))".
						".*># Uis", $this->html, $matches
					  );

		$tags = array();
		for ( $x = 0; $x < count($matches[0]); $x++ ) {
			$meta_name  = strtolower(trim($matches[1][$x]));
			$meta_value = strtolower(trim($matches[2][$x]));
			$tags[$meta_name] = $meta_value;
		}
		return $tags;
  	}

	/*
	Function: 	getMetaTagAttributes
	Purpose: 	Use this when you want the entire nodeValue with HTML code
	Returns: 	the nodeValue with including HTML (if it has any).
	*/
	public function getMetaTagAttributes( $tag_type, $node = null , $get_img_tag = true ) 
	{ 
		$attributes = array();
		
		if ( $node == null ) {
			$this->loadDom(true);
			// $the_node = $this->domDoc->getElementsByTagName('$tag_type')->item(0);
			
			if ( $tag_type == 'link') {
				$the_node = $this->domDoc->getElementsByTagName('$tag_type')->item(0);
			} else {
				$the_node = $this->domDoc->getElementsByTagName("$tag_type")->item(0);
			}
		} else {
			$the_node = $node;
		}
		
		$xpath = new DOMXPath($this->domDoc);
		$query = "//$tag_type/@*";
		$list = $xpath->query($query);
		
		foreach ($list as $attr) {
			$attributes[$attr->nodeName] = $attr->nodeValue;
		}
		
		if ( $get_img_tag ) {
			$img_tag = $this->getImgTag();
			if ( $img_tag !== null ) {
				$attributes['img_tag'] = $img_tag;
			}
		}

		return $attributes;
	}

	/*
	Function: 	findATagNode
	Purpose: 	Gets all the meta data from the given html source.
	Returns: 	Associative array conatining all found meta attributes.
	*/
	public function findATagNode( $array = null )
	{	
		foreach ($array as $value) {
			if ( substr($value, 0, 3) == "<a " ) {
				return $value;
			}
		}
		return null;
	}

	/*
	Function: 	getChildNodesArray
	Purpose: 	Use this when you want the entire nodeValue with HTML code
	Returns: 	the nodeValue with including HTML (if it has any).
	*/
	public function getChildNodesArray( $node ) 
	{ 
		$doc = $node->ownerDocument;
		$child_node_array = array();
		
		foreach ($node->childNodes as $child) { 
			$child_node_array[] = $doc->saveXML($child);
		}
		return $child_node_array;
	}

	/*
	Function: 	getImgTag
	Purpose: 	Use this when you want the entire nodeValue with HTML code
	Returns: 	the nodeValue with including HTML (if it has any).
	*/
	public function getImgTag() 
	{ 
		$begin = strpos($this->html, "<img ");
		
		if ( !$begin ) {
			return false;
		} else {
			$end 		= strpos($this->html, ">", $begin);
			$length 	= $end - $begin + 1;
			$img_tag 	= substr($this->html, $begin, $length);
			return $img_tag;
		}
	}

	/*
	Function: 	getATag
	Purpose: 	Use this when you want the entire nodeValue with HTML code
	Returns: 	the nodeValue with including HTML (if it has any).
	*/
	public function getATag( $node = null ) 
	{ 
		$begin = strpos($node, "<a ");
		
		if ( !$begin ) {
			return false;
		} else {
			$end 		= strpos($node, ">", $begin);
			$length 	= $end - $begin + 1;
			$a_tag 		= substr($node, $begin, $length);
			return $a_tag;
		}
	}

	
} // end of ScraperMoreFaster class

?>
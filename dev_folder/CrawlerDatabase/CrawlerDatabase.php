<?php

/*
Class: 		CrawlerDatabase
Author: 	Joe Bergevin (joe@seo.com)
Purpose: 	Formal hook into the Crawler Database.
	
Call Examples: 	
	
*/

require_once("config.php");
$file_path = dirname(__FILE__);



/* ######################################################################
   ################# Documentation for Setting Parameters ###############
   ######################################################################
	
	

   
   ######################################################################
   ####################### End Documentation ############################
   ###################################################################### */
	
	

class CrawlerDatabase 
{
	protected $crawler;
	protected $crawls_id;
	protected $campaigns_id;
	protected $mysqli;
	protected $pages_to_crawl;
	protected $crawl_report;
	public $crawl_details = array();
	
	/*
	Function: __construct
	Purpose: Pre-set additional properties for the Crawl
	*/
	public function __construct() 
	{
		$this->mysqli = new mysqli(
			SERVER_ADDRESS, USER_NAME, DB_PASSWORD, DB_NAME
		) or die(
			mysql_error . ' could not connect to database'
		);

	}

	
	/*
	Function: setCampaignsId
	Purpose: 
	*/
	public function setCampaignsId( $campaigns_id ) 
	{
		$this->campaigns_id = $campaigns_id;
		return;
	} // end of setCampaignsId function

	/*
	Function: setCampaignsId
	Purpose: 
	*/
	public function setCrawlsId( $crawls_id ) 
	{
		$this->crawls_id = $crawls_id;
		return;
	} // end of setCrawlsId function

	/*
	Function: 	getCrawlerData
	Purpose:  	This is the dispatcher. It takes the string in $func_param and
				calls a function from it.
	Example: 	$func_param = 'click_here', this will call getClickHere().
	*/
	public function getCrawlerData( $func_param ) 
	{
		$getFuncParam = str_replace("_", " ", $func_param);
		$getFuncParam = ucwords($getFuncParam);
		$getFuncParam = "get" .str_replace(" ", "", $getFuncParam);

		$requested_data = $this->$getFuncParam();
		return $requested_data;
	} // end of getCrawlerData function


/*************************************************

********ONLY EDIT BELOW THIS POINT*****************

**************************************************/
	/*
	Function: 	getClickHere
	Purpose: 	% of anchors that contain "click here" anchor text
	*/
	private function getClickHere() 
	{
		
		// sql statement to pull the related data from the crawler db
		$sql = "SELECT((SELECT count(a.trimmed_anchor) FROM cr_anchors a
				INNER JOIN cr_a_tags at ON a.id = at.anchors_id
				INNER JOIN cr_crawled_pages cp ON at.crawled_pages_id = cp.id
				INNER JOIN cr_crawls c ON cp.crawls_id = c.id
				WHERE a.trimmed_anchor LIKE '%click here%'
				AND c.id = $this->crawls_id)/(SELECT count(a.trimmed_anchor) FROM cr_anchors a
				INNER JOIN cr_a_tags at ON a.id = at.anchors_id
				INNER JOIN cr_crawled_pages cp ON at.crawled_pages_id = cp.id
				INNER JOIN cr_crawls c ON cp.crawls_id = c.id
				AND c.id = $this->crawls_id))AS avg
			FROM cr_crawled_pages cp
		WHERE 1 
		AND cp.crawls_id =$this->crawls_id
		LIMIT 1";

		$results = $this->mysqli->query( $sql );
		if($results)
    	$row = $results->fetch_array(MYSQLI_NUM);
        $calculation = $row[0];		
		// calculate the requested data based on the $results
		$requested_data = $calculation; 
		
		// return the requested_data value
		return $requested_data;
	} // end of getClickHere function

	/*
	Function: 	getRelativeUrl
	Purpose: 	% of links that use the relative URL 
	*/
	private function getRelativeUrl() 
	{
		// sql statement to pull the related data from the crawler db
		$sql = "SELECT((
						SELECT COUNT( * ) 
						FROM cr_a_tags ct
						WHERE raw_href NOT LIKE  '%http://%')
						/
						(SELECT count(*) FROM cr_a_tags))
					 AS avg
						FROM cr_a_tags ct
					INNER JOIN
					`cr_crawled_pages` cp
						ON
						ct.crawled_pages_id = cp.id
					WHERE 1
					AND
					cp.crawls_id = $this->crawls_id 
					LIMIT 1";

		$results = $this->mysqli->query( $sql );
		if($results)
    	$row = $results->fetch_array(MYSQLI_NUM);
        $calculation = $row[0];
   	
		// calculate the requested data based on the $results
		$requested_data = $calculation; 
		
		// return the requested_data value
		return $requested_data;
	} // end of getRelativeUrl function

	/*
	Function: 	getUrlLength
	Purpose: 	% of URLs containing 70+ characters
	*/
	private function getUrlLength() 
	{
		// sql statement to pull the related data from the crawler db
		$sql = "SELECT ((SELECT count(*) FROM `cr_urls` cp where CHAR_LENGTH(rebuilt_url)>70)/(SELECT count(*) FROM `cr_urls` cp)) as avg from cr_urls cr					
					INNER JOIN
					`cr_crawled_pages` cp
						ON
						cr.id = cp.urls_id
					WHERE 1
					AND
					cp.crawls_id = $this->crawls_id LIMIT 1";

		$results = $this->mysqli->query( $sql );
		if($results)		
    	$row = $results->fetch_array(MYSQLI_NUM);
        $calculation = $row[0];		
		// calculate the requested data based on the $results
		$requested_data = $calculation; 
		
		// return the requested_data value
		return $requested_data;
	} // end of getUrlLength function

	/*
	Function: 	getMetaKeywords
	Purpose: 	% of URLs containing meta keywords
	*/
	private function getMetaKeywords() 
	{
		// sql statement to pull the related data from the crawler db
		$sql = "SELECT 
				((
				SELECT count(*) FROM cr_crawled_pages c
				INNER JOIN cr_crawled_pages_metas cm
				ON cm.crawled_pages_id = c.id
				AND cm.`key`  like '%keyword%'
				)
				/(SELECT COUNT(*) FROM cr_crawled_pages)) as AVG FROM cr_crawled_pages cp 
				WHERE 1 AND cp.crawls_id = $this->crawls_id
				LIMIT 1";

		$results = $this->mysqli->query( $sql );
		if($results)		
    	$row = $results->fetch_array(MYSQLI_NUM);
        $calculation = $row[0];		
		// calculate the requested data based on the $results
		$requested_data = $calculation; 
		
		// return the requested_data value
		return $requested_data;
	} // end of getMetaKeywords function

	/*
	Function: 	getDupMetaKeywords
	Purpose: 	% of URLs with duplicate meta keywords
	*/
	private function getDupMetaKeywords() 
	{
		// sql statement to pull the related data from the crawler db
		$sql = "SELECT (
			(
			SELECT COUNT( * ) 
			FROM cr_crawled_pages c
			INNER JOIN cr_crawled_pages_metas cm ON cm.crawled_pages_id = c.id
			AND cm.`key` LIKE  '%keyword%'
			GROUP BY cm.value
			HAVING COUNT( cm.id ) >1
			LIMIT 1
			) / ( 
			SELECT COUNT( * ) 
			FROM cr_crawled_pages )
			) AS AVG
			FROM cr_crawled_pages cp
			WHERE 1 
				AND cp.crawls_id = $this->crawls_id
				LIMIT 1";

		$results = $this->mysqli->query( $sql );
		if($results)		
    	$row = $results->fetch_array(MYSQLI_NUM);
        $calculation = $row[0];		
		// calculate the requested data based on the $results
		$requested_data = $calculation; 
		
		// return the requested_data value
		return $requested_data;
	} // end of getDupMetaKeywords function

	/*
	Function: 	getImgAlt
	Purpose: 	% of images containing alt text
	*/
	private function getImgAlt() 
	{
		// sql statement to pull the related data from the crawler db
		$sql = "SELECT 
				(
					(
						SELECT COUNT( * ) 
						FROM cr_crawled_pages c
						INNER JOIN  `cr_img_tags_metas` cm ON cm.crawled_pages_id = c.id
						AND cm.key =  'alt'
						AND cm.value !=  ''
						WHERE 1 
						AND c.crawls_id =$this->crawls_id
					) / 
					( 
						SELECT COUNT( * ) 
						FROM cr_crawled_pages c
						INNER JOIN  `cr_img_tags_metas` cm ON cm.crawled_pages_id = c.id
						WHERE 1 
						AND c.crawls_id =$this->crawls_id 
					)
				) AS avg
				FROM cr_crawled_pages cp
				WHERE 1 
				AND cp.crawls_id =$this->crawls_id
				LIMIT 1";

		$results = $this->mysqli->query( $sql );
		if($results)		
    	$row = $results->fetch_array(MYSQLI_NUM);
        $calculation = $row[0];		
		// calculate the requested data based on the $results
		$requested_data = $calculation; 
		
		// return the requested_data value
		return $requested_data;
	} // end of getImgAlt function

	/*
	Function: 	getImgAltNameDup
	Purpose: 	% of images with alt text and file names that match
	*/
	private function getImgAltNameDup() 
	{
		// sql statement to pull the related data from the crawler db
		$sql = "SELECT (
			(
			SELECT COUNT( * ) 
			FROM cr_crawled_pages c
			INNER JOIN  `cr_img_tags_metas` cm ON cm.crawled_pages_id = c.id
			AND cm.key =  'alt'
			AND cm.value !=  ''
			WHERE 1 
			AND c.crawls_id =$this->crawls_id
			GROUP BY cm.value
			HAVING COUNT( cm.id ) >1
			LIMIT 1
			) / ( 
			SELECT COUNT( * ) 
			FROM cr_crawled_pages c
			INNER JOIN  `cr_img_tags_metas` cm ON cm.crawled_pages_id = c.id
			WHERE 1 
			AND c.crawls_id =$this->crawls_id )
			) AS avg
			FROM cr_crawled_pages cp
		WHERE 1 
		AND cp.crawls_id =$this->crawls_id
		LIMIT 1";

		$results = $this->mysqli->query( $sql );
		if($results)		
    	$row = $results->fetch_array(MYSQLI_NUM);
        $calculation = $row[0];		
		// calculate the requested data based on the $results
		$requested_data = $calculation; 
		
		// return the requested_data value
		return $requested_data;
	} // end of getImgAltNameDup function

	/*
	Function: 	getResponseCodes
	Purpose: 	% of pages with non-[200/301] response codes
	*/
	private function getResponseCodes() 
	{
		// sql statement to pull the related data from the crawler db
		$sql = "SELECT ((SELECT count(*) FROM `cr_crawled_pages` WHERE 1
					 AND response_code != 200 AND response_code != 301)
					 /
					 (SELECT count(*) FROM `cr_crawled_pages` WHERE 1)) 
				AS avg FROM `cr_crawled_pages` cp
				WHERE 1 
				AND cp.crawls_id =$this->crawls_id
				LIMIT 1";

		$results = $this->mysqli->query( $sql );		
		if($results)		
    	$row = $results->fetch_array(MYSQLI_NUM);
        $calculation = $row[0];		
		// calculate the requested data based on the $results
		$requested_data = $calculation; 
		
		// return the requested_data value
		return $requested_data;
	} // end of getResponseCodes function

	/*
	Function: 	getRobotsExists
	Purpose: 	Does /robots.txt exist?
	*/
	private function getRobotsExists() 
	{
		// sql statement to pull the related data from the crawler db
		$sql = "";

		//$results = $this->mysqli->query( $sql );
		
		// calculate the requested data based on the $results
		$requested_data = $calculation; 
		
		// return the requested_data value
		return $requested_data;
	} // end of getRobotsExists function

	/*
	Function: 	getRobotsNoindexNofollow
	Purpose: 	% of pages with a robots tag containing "noindex" or "nofollow"
	*/
	private function getRobotsNoindexNofollow() 
	{
		// sql statement to pull the related data from the crawler db
		$sql = "";

		//$results = $this->mysqli->query( $sql );
		
		// calculate the requested data based on the $results
		$requested_data = $calculation; 
		
		// return the requested_data value
		return $requested_data;
	} // end of getRobotsNoindexNofollow function

	/*
	Function: 	getAnalyticsInstalled
	Purpose: 	% of pages Analytics is installed on
	*/
	private function getAnalyticsInstalled() 
	{
		// sql statement to pull the related data from the crawler db
		$sql = "";

		//$results = $this->mysqli->query( $sql );
		
		// calculate the requested data based on the $results
		$requested_data = $calculation; 
		
		// return the requested_data value
		return $requested_data;
	} // end of getAnalyticsInstalled function

	/*
	Function: 	getAnalyticsCustomTracking
	Purpose: 	% of pages that include custom variables and/or event 
				tracking functions in GA script
	*/
	private function getAnalyticsCustomTracking() 
	{
		// sql statement to pull the related data from the crawler db
		$sql = "";

		//$results = $this->mysqli->query( $sql );
		
		// calculate the requested data based on the $results
		$requested_data = $calculation; 
		
		// return the requested_data value
		return $requested_data;
	} // end of getAnalyticsCustomTracking function

	/*
	Function: 	getSocialProfiles
	Purpose: 	% of pages that link to social profiles (currently only FaceBook and Twitter)
	*/
	private function getSocialProfiles() 
	{
		// sql statement to pull the related data from the crawler db
		$sql = "SELECT((
						SELECT COUNT( * ) 
						FROM cr_a_tags ct
						WHERE 1 AND raw_href LIKE  '%facebook%'
						OR raw_href LIKE  '%twitter%' 
						OR raw_href LIKE  '%plus.google%')
						/
						(SELECT count(*) FROM cr_a_tags))
					 AS avg
						FROM cr_a_tags ct
					INNER JOIN
					`cr_crawled_pages` cp
						ON
						ct.crawled_pages_id = cp.id
					WHERE 1
					AND
					cp.crawls_id = $this->crawls_id 
					LIMIT 1";

		$results = $this->mysqli->query( $sql );
		if($results)
    	$row = $results->fetch_array(MYSQLI_NUM);
        $calculation = $row[0];
   	
		// calculate the requested data based on the $results
		$requested_data = $calculation; 
		
		// return the requested_data value
		return $requested_data;
	} // end of getSocialProfiles function

	/*
	Function: 	getSocialSharingButtonsInstalled
	Purpose: 	% of pages with social sharing buttons installed
	*/
	private function getSocialSharingButtonsInstalled() 
	{
		// sql statement to pull the related data from the crawler db
		$sql = "SELECT((
						SELECT COUNT( * ) 
						FROM cr_img_tags ct
						WHERE 1 AND raw_img_tag LIKE  '%facebook%'
						OR raw_img_tag LIKE  '%twitter%' 
						OR raw_img_tag LIKE  '%plus.google%')
						/
						(SELECT count(*) FROM cr_img_tags))
					 AS avg
						FROM cr_img_tags ct
					INNER JOIN
					`cr_crawled_pages` cp
						ON
						ct.crawled_pages_id = cp.id
					WHERE 1
					AND
					cp.crawls_id = $this->crawls_id 
					LIMIT 1";

		$results = $this->mysqli->query( $sql );
		if($results)
    	$row = $results->fetch_array(MYSQLI_NUM);
        $calculation = $row[0];
   	
		// calculate the requested data based on the $results
		$requested_data = $calculation; 
		
		// return the requested_data value
		return $requested_data;
	} // end of getSocialSharingButtonsInstalled function

	/*
	Function: 	getCanonicalDuplicateContent (leave this one)
	Purpose: 	% of pages with a unique canonical tag (not to itself) that 
				references a page containing duplicate content
	*/
	private function getCanonicalDuplicateContent() 
	{
		// sql statement to pull the related data from the crawler db
		$sql = "";

		//$results = $this->mysqli->query( $sql );
		
		// calculate the requested data based on the $results
		$requested_data = $calculation; 
		
		// return the requested_data value
		return $requested_data;
	} // end of getCanonicalDuplicateContent function

	/*
	Function: 	getUrlsSpammy (leave this one)
	Purpose: 	% of URLs that are Spammy (URLs that have a repeating 
				non-'stop'-word, e.g. the word 'viagra' in 
				/viagra-pills-viagra-reviews)
	*/
	private function getUrlsSpammy() 
	{
		// sql statement to pull the related data from the crawler db
		$sql = "";

		//$results = $this->mysqli->query( $sql );
		
		// calculate the requested data based on the $results
		$requested_data = $calculation; 
		
		// return the requested_data value
		return $requested_data;
	} // end of getUrlsSpammy function

	/*
	Function: 	getXmlSitemapExists
	Purpose: 	Does XML sitemap exist (checked /robots.txt reference, or common filenames like /sitemap.xml, /sitemap_index.xml, etc.)?
	*/
	private function getXmlSitemapExists() 
	{
		// sql statement to pull the related data from the crawler db
		$sql = "";

		//$results = $this->mysqli->query( $sql );
		
		// calculate the requested data based on the $results
		$requested_data = $calculation; 
		
		// return the requested_data value
		return $requested_data;
	} // end of getXmlSitemapExists function

	/*
	Function: 	getXmlSitemap200
	Purpose: 	XML Sitemap: % of URLs that return a proper 200
	*/
	private function getXmlSitemap200() 
	{
		// sql statement to pull the related data from the crawler db
		$sql = "";

		//$results = $this->mysqli->query( $sql );
		
		// calculate the requested data based on the $results
		$requested_data = $calculation; 
		
		// return the requested_data value
		return $requested_data;
	} // end of getXmlSitemap200 function

} // end of CrawlerDatabase class


?>
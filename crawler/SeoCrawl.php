<?php

/*
Class: 		SeoCrawl
Author: 	Joe Bergevin (joe@joescode.com)
Purpose: 	Set parameters and then start an SEO Crawl.
	
Call Examples: 	
	
*/

set_time_limit(0);
error_reporting(E_ALL);
require_once("config.php");
$file_path = dirname(__FILE__);
require_once("$file_path/../my_crawler_extends.php");



/* ######################################################################
   ################# Documentation for Setting Parameters ###############
   ######################################################################
	
	

   
   ######################################################################
   ####################### End Documentation ############################
   ###################################################################### */
	
	

class SeoCrawl 
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
		$this->crawler = new MyCrawler();
		$this->crawler->setWorkingDirectory(WORKING_DIRECTORY); 
		$this->crawler->setUrlCacheType(PHPCrawlerUrlCacheTypes::URLCACHE_SQLITE);
		$this->crawler->addContentTypeReceiveRule(CONTENT_TYPE);
		$this->crawler->enableCookieHandling(true);
		$this->mysqli = new mysqli(SERVER_ADDRESS, USER_NAME, DB_PASSWORD, DB_NAME) or die(mysql_error . ' could not connect to database');
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
	Function: setStartingURL
	Purpose: 
	*/
	public function setStartingURL( $url ) 
	{
		$this->crawler->setURL($url);
		return;
	} // end of setStartingURL function

	/*
	Function: setPagesToCrawl
	Purpose: 
	*/
	public function setPagesToCrawl( $limit ) 
	{
		$this->crawler->setPageLimit($limit);
		$this->pages_to_crawl = $limit;
		return;
	} // end of setPagesToCrawl function

	/*
	Function: beginCrawl
	Purpose: begins the crawl
	Expects: parameters for crawl must be set first
	*/
	public function beginCrawl() 
	{
		$this->crawls_id = $this->setupDatabaseCrawlVar();
		$this->crawler->crawls_id = $this->crawls_id;
		

		$this->crawler->go();
		die('here');
		return;
	} // end of beginCrawl function

	/*
	Function: setupDatabaseCrawlVar
	Purpose: 
	*/
	public function setupDatabaseCrawlVar() 
	{
		$this->crawl_details = 
			array(
				'pages_to_crawl' => $this->pages_to_crawl,
				'crawl_finished' => false 
			);
		$crawl_details_ser = serialize($this->crawl_details);
		$sql 	=  "INSERT INTO cr_crawls (`campaigns_id`, `crawl_details`) 
					VALUES ('$this->campaigns_id', '$crawl_details_ser')";
		$this->mysqli->query( $sql );

		return $this->mysqli->insert_id;
	} // end of setupDatabaseCrawlVar function
	
	/*
	Function: getCrawlStatus
	Purpose: begins the crawl
	Expects: parameters for crawl must be set first
	*/
	public function getCrawlStatus() 
	{
		$this->crawl_report = $this->crawler->getProcessReport();
		return $this->crawl_report;
	} // end of getCrawlStatus function

	/*
	Function: storeCrawlFinishedDetails
	Purpose: 
	*/
	public function storeCrawlFinishedDetails() 
	{
		$this->crawl_details['pages_to_crawl'] = $this->pages_to_crawl;
		$this->crawl_details['crawl_finished'] = date("Y-m-d H:i:s");
		$this->crawl_details['links_followed'] = $this->crawl_report->links_followed;
		$this->crawl_details['files_received'] = $this->crawl_report->files_received;
		$this->crawl_details['process_runtime'] = $this->crawl_report->process_runtime;

		$crawl_details_ser = serialize($this->crawl_details);
		$sql 	=  "UPDATE cr_crawls 
					SET crawl_details = '$crawl_details_ser'
					WHERE id='this->crawls_id'";
		$this->mysqli->query( $sql );

		return $this->crawl_details;
	} // end of storeCrawlFinishedDetails function

} // end of ProminentKeywords class

?>
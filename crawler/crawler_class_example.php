<?php
error_reporting(E_ALL);

$file_path = dirname(__FILE__);


//$file_path = "http://crawler.seo.com/crawler/classes/SeoCrawl/";
require_once("$file_path/classes/SeoCrawl/SeoCrawl.php");


$campaigns_id 	= 1;
$url 			= "www.seo.com";
$limit			= 0;




$seocrawl = new SeoCrawl();

$seocrawl->setCampaignsId($campaigns_id);
$seocrawl->setStartingURL($url);
$seocrawl->setPagesToCrawl($limit); // 0 for no limit
$seocrawl->beginCrawl();

$report = $seocrawl->getCrawlStatus();
$crawl_details = $seocrawl->storeCrawlFinishedDetails();

/*if (PHP_SAPI == "cli") {
	$lb = "\n";
} else {
	$lb = "<br />";
}
    
echo "Summary:".$lb;
echo "Links followed: ".$report->links_followed.$lb;	
echo "Documents received: ".$report->files_received.$lb;
echo "Bytes received: ".$report->bytes_received." bytes".$lb;
echo "Process runtime: ".$report->process_runtime." sec".$lb; 
*/

echo "<p>Final Crawl Details Array: </p><pre>";
print_r($crawl_details);

?>
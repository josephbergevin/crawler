<?php

// It may take a while to crawl a site ...
set_time_limit(0);
error_reporting(E_ALL);
// Inculde the phpcrawl-mainclass

require_once("classes/my_crawler_extends.php");

// Now, create a instance of your class, define the behaviour
// of the crawler (see class-reference for more options and details)
// and start the crawling-process. 

$crawler = new MyCrawler();

// URL to crawl

$crawler->setWorkingDirectory("/var/www/code/crawler/seocrawler/tmp/"); 
$crawler->setUrlCacheType(PHPCrawlerUrlCacheTypes::URLCACHE_SQLITE);
$crawler->setURL("www.hapari.com");
// Only receive content of files with content-type "text/html"
$crawler->addContentTypeReceiveRule("#text/html#");

// Ignore links to pictures, dont even request pictures
// $crawler->addURLFilterRule("#\.(jpg|jpeg|gif|png)$# i");

// Store and send cookie-data like a browser does
$crawler->enableCookieHandling(true);

// Set the traffic-limit to 1 MB (in bytes,
// for testing we dont want to "suck" the whole site)
//$crawler->setTrafficLimit(1 * 1024);
$crawler->setPageLimit(10);

// Thats enough, now here we go
$crawler->go();

// At the end, after the process is finished, we print a short
// report (see method getProcessReport() for more information)
$report = $crawler->getProcessReport();

if (PHP_SAPI == "cli") $lb = "\n";
else $lb = "<br />";
    
	echo "Summary:".$lb;
	echo "Links followed: ".$report->links_followed.$lb;	
	echo "Documents received: ".$report->files_received.$lb;
	echo "Bytes received: ".$report->bytes_received." bytes".$lb;
	echo "Process runtime: ".$report->process_runtime." sec".$lb; 
?>

<?php


// Inculde the phpcrawl-mainclass
include("/classes/mycrawler_class.php");



// Now, create a instance of your class, define the behaviour
// of the crawler (see class-reference for more options and details)
// and start the crawling-process. 

$crawler = new MyCrawler(); 

// URL to crawl
$crawler->setURL("www.seo.com");

// Only receive content of files with content-type "text/html"
$crawler->addContentTypeReceiveRule("#text/html#");

// Ignore links to pictures, dont even request pictures
// $crawler->addURLFilterRule("#\.(jpg|jpeg|gif|png)$# i");

// Store and send cookie-data like a browser does
$crawler->enableCookieHandling(true);

// Set the traffic-limit to 1 MB (in bytes,
// for testing we dont want to "suck" the whole site)
//$crawler->setTrafficLimit(1 * 1024);
$crawler->setPageLimit(1);

// Thats enough, now here we go
$crawler->go();

// note...




?>
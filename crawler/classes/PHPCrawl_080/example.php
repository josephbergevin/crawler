<?php

// It may take a while to crawl a site ...
//set_time_limit(10000);

// Inculde the phpcrawl-mainclass
include("libs/PHPCrawler.class.php");

// Extend the class and override the handleDocumentInfo()-method 
class MyCrawler extends PHPCrawler 
{
  function handleDocumentInfo($DocInfo) 
  {
    // Just detect linebreak for output ("\n" in CLI-mode, otherwise "<br>").
    if (PHP_SAPI == "cli") $lb = "\n";
    else $lb = "<br />";

    // Print the URL and the HTTP-status-Code
    echo "Page requested: ".$DocInfo->url." (".$DocInfo->http_status_code.")".$lb;
    
    // Print the refering URL
    echo "Referer-page: ".$DocInfo->referer_url.$lb;
    
    // Print if the content of the document was be recieved or not
    if ($DocInfo->received == true)
      echo "Content received: ".$DocInfo->bytes_received." bytes".$lb;
    else
      echo "Content not received".$lb; 
    
    // Now you should do something with the content of the actual
    // received page or file ($DocInfo->source), we skip it in this example 
    
    echo $lb;
    echo "<p>bytes_received: " .$DocInfo->url ."</p>";
	//print_r( $DocInfo->responseHeader );
	echo "<p>bytes_received: " .$DocInfo->bytes_received ."</p>";
	echo "<p>content_type: " .$DocInfo->content_type ."</p>";
	echo "<p>referer_url: " .$DocInfo->referer_url ."</p>";
	echo "<p>refering_linkcode: " .$DocInfo->refering_linkcode ."</p>";
	echo "<p>refering_link_raw: " .$DocInfo->refering_link_raw ."</p>";
	echo "<p>refering_linktext: " .$DocInfo->refering_linktext ."</p>";
	echo "<p>data_transfer_time: " .$DocInfo->data_transfer_time ."</p>";
	//print_r( $DocInfo->meta_attributes );
	print_r( $DocInfo->links_found );
  //print_r( $DocInfo );
    flush();
  } 
}

// Now, create a instance of your class, define the behaviour
// of the crawler (see class-reference for more options and details)
// and start the crawling-process. 

$crawler = new MyCrawler();

// URL to crawl
$crawler->setURL("www.seo.com");

// Only receive content of files with content-type "text/html"
$crawler->addContentTypeReceiveRule("#text/html#");

// Ignore links to pictures, dont even request pictures
$crawler->addURLFilterRule("#\.(jpg|jpeg|gif|png)$# i");

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
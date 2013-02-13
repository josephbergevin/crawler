<?php 

// error_reporting(E_ERROR);
error_reporting(E_ALL);

require_once "ScraperMoreFaster.php";

$url = "http://www.seo.com/blog";
// $link = "<link rel=\"canonical\" href=\"http://www.hapari.com/hapari-wholesale-login.html\" />";
$link = "<a href=\"/home/\"><img src=\"/skin1/images/hapari-logo.png\" alt=\"Hapari Swimwear Logo\" /></a>";

$scrapermorefaster = New ScraperMoreFaster;

$scrapermorefaster->str_get_html($link);
// $scrapermorefaster->file_get_html($url);



/*echo "<p>Title: " .$scrapermorefaster->getMetaTagAttributes() ."</p>";
echo "<a href=\"/\"><img src=\"/skin1/images/hapari-logo.png\" alt=\"Hapari Swimwear Logo\" /></a>";*/
// echo "<p>" .$scrapermorefaster->innerHTMLDom() ."</p>";
// print_r($scrapermorefaster->getChildNodes());
echo $scrapermorefaster->getImgTag();


/*die();

$time_start = microtime(true);

$headings1 = $scrapermorefaster->getHeadingTags();

$current_time = microtime(true);
$time_passed1 = $current_time - $time_start;


// ***************************

$scrapermorefaster->loadDom();
$time_start = microtime(true);

$headings2 = $scrapermorefaster->getHeadingTagsDom();

$current_time = microtime(true);
$time_passed2 = $current_time - $time_start;

echo "<p>PhpDom: $time_passed1</p>";
echo "<p>RegEx: $time_passed2</p>";

echo "<pre>";
print_r($headings1);
echo "<p>--------------------------------------------------------</p>";
print_r($headings2);
*/

?>
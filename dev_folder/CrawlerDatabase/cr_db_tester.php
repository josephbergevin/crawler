<?php


require_once("CrawlerDatabase.php");

$cr_db = new CrawlerDatabase;

$cr_db->setCrawlsId(25);

echo "<p>ClickHere: " .strval($cr_db->getCrawlerData('click_here') * 100) ."%</p>";

echo "<p>RelativeUrl: " .strval($cr_db->getCrawlerData('relative_url') * 100) ."%</p>";

echo "<p>UrlLength: " .strval($cr_db->getCrawlerData('url_length') * 100) ."%</p>";

echo "<p>MetaKeywords: " .strval($cr_db->getCrawlerData('meta_keywords') * 100) ."%</p>";

echo "<p>DupMetaKeywords: " .strval($cr_db->getCrawlerData('dup_meta_keywords') * 100) ."%</p>";

echo "<p>ImgAlt: " .strval($cr_db->getCrawlerData('img_alt') * 100) ."%</p>";

echo "<p>ImgAltNameDup: " .strval($cr_db->getCrawlerData('img_alt_name_dup') * 100) ."%</p>";

echo "<p>ResponseCodes: " .strval($cr_db->getCrawlerData('response_codes') * 100) ."%</p>";

echo "<p>RobotsNoindexNofollow: " .strval($cr_db->getCrawlerData('robots_noindex_nofollow') * 100) ."%</p>";

echo "<p>AnalyticsInstalled: " .strval($cr_db->getCrawlerData('analytics_installed') * 100) ."%</p>";

echo "<p>AnalyticsCustomTracking: " .strval($cr_db->getCrawlerData('analytics_custom_tracking') * 100) ."%</p>";

echo "<p>SocialProfiles: " .strval($cr_db->getCrawlerData('social_profiles') * 100) ."%</p>";

echo "<p>SocialSharingButtonsInstalled: " .strval($cr_db->getCrawlerData('social_sharing_buttons_installed') * 100) ."%</p>";

echo "<p>CanonicalDuplicateContent: " .strval($cr_db->getCrawlerData('canonical_duplicate_content') * 100) ."%</p>";

echo "<p>UrlsSpammy: " .strval($cr_db->getCrawlerData('urls_spammy') * 100) ."%</p>";

echo "<p>XmlSitemap200: " .strval($cr_db->getCrawlerData('xml_sitemap_200') * 100) ."%</p>";


echo "<p>RobotsExists: ";
if ($cr_db->getCrawlerData('robots_exists')) {
	"yes</p>";
} else {
	"no</p>";
}

echo "<p>XmlSitemapExists: ";
if ($cr_db->getCrawlerData('xml_sitemap_exists')) {
	"yes</p>";
} else {
	"no</p>";
}















?>
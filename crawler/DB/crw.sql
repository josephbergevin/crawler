-- MySQL dump 10.13  Distrib 5.5.28, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: crawler
-- ------------------------------------------------------
-- Server version	5.5.28-0ubuntu0.12.04.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bookmark_views`
--

DROP TABLE IF EXISTS `bookmark_views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bookmark_views` (
  `bkviews_id` int(11) NOT NULL AUTO_INCREMENT,
  `filter_array` text,
  PRIMARY KEY (`bkviews_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookmark_views`
--

LOCK TABLES `bookmark_views` WRITE;
/*!40000 ALTER TABLE `bookmark_views` DISABLE KEYS */;
/*!40000 ALTER TABLE `bookmark_views` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cr_a_tags`
--

DROP TABLE IF EXISTS `cr_a_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cr_a_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urls_id` int(11) NOT NULL,
  `raw_href` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `relative` int(11) NOT NULL COMMENT '0=false; 1=true',
  `anchors_id` int(11) NOT NULL,
  `raw_anchor` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `imgs_id` int(11) NOT NULL,
  `crawled_pages_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cr_a_tags`
--

LOCK TABLES `cr_a_tags` WRITE;
/*!40000 ALTER TABLE `cr_a_tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `cr_a_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cr_a_tags_metas`
--

DROP TABLE IF EXISTS `cr_a_tags_metas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cr_a_tags_metas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `a_tags_id` int(11) NOT NULL,
  `key` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cr_a_tags_metas`
--

LOCK TABLES `cr_a_tags_metas` WRITE;
/*!40000 ALTER TABLE `cr_a_tags_metas` DISABLE KEYS */;
/*!40000 ALTER TABLE `cr_a_tags_metas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cr_anchor_recommends`
--

DROP TABLE IF EXISTS `cr_anchor_recommends`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cr_anchor_recommends` (
  `reccomend_id` int(11) NOT NULL AUTO_INCREMENT,
  `anchor_id` int(11) NOT NULL,
  `old_url_id` int(11) NOT NULL,
  `new_url_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`reccomend_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cr_anchor_recommends`
--

LOCK TABLES `cr_anchor_recommends` WRITE;
/*!40000 ALTER TABLE `cr_anchor_recommends` DISABLE KEYS */;
INSERT INTO `cr_anchor_recommends` VALUES (1,39,495,39,'2012-11-13 21:13:47'),(2,77,454,77,'2012-11-14 19:12:39');
/*!40000 ALTER TABLE `cr_anchor_recommends` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cr_anchors`
--

DROP TABLE IF EXISTS `cr_anchors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cr_anchors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trimmed_anchor` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cr_anchors`
--

LOCK TABLES `cr_anchors` WRITE;
/*!40000 ALTER TABLE `cr_anchors` DISABLE KEYS */;
/*!40000 ALTER TABLE `cr_anchors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cr_campaigns`
--

DROP TABLE IF EXISTS `cr_campaigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cr_campaigns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clients_id` int(11) NOT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `base_url` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cr_campaigns`
--

LOCK TABLES `cr_campaigns` WRITE;
/*!40000 ALTER TABLE `cr_campaigns` DISABLE KEYS */;
INSERT INTO `cr_campaigns` VALUES (1,2,'seo.com','www.seo.com','0000-00-00 00:00:00'),(2,1,'hapari.com','www.hapari.com','0000-00-00 00:00:00'),(3,0,'','','0000-00-00 00:00:00'),(4,2,'joescode','joescode.com','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `cr_campaigns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cr_client_accounts`
--

DROP TABLE IF EXISTS `cr_client_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cr_client_accounts` (
  `id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cr_client_accounts`
--

LOCK TABLES `cr_client_accounts` WRITE;
/*!40000 ALTER TABLE `cr_client_accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `cr_client_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cr_clients`
--

DROP TABLE IF EXISTS `cr_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cr_clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) NOT NULL,
  `primary_contact_fname` varchar(255) NOT NULL,
  `primary_contact_lname` varchar(255) NOT NULL,
  `primary_contact_email` varchar(255) NOT NULL,
  `primary_contact_phone` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cr_clients`
--

LOCK TABLES `cr_clients` WRITE;
/*!40000 ALTER TABLE `cr_clients` DISABLE KEYS */;
/*!40000 ALTER TABLE `cr_clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cr_cookies`
--

DROP TABLE IF EXISTS `cr_cookies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cr_cookies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `crawled_pages_id` int(11) NOT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cr_cookies`
--

LOCK TABLES `cr_cookies` WRITE;
/*!40000 ALTER TABLE `cr_cookies` DISABLE KEYS */;
/*!40000 ALTER TABLE `cr_cookies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cr_crawled_pages`
--

DROP TABLE IF EXISTS `cr_crawled_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cr_crawled_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `crawls_id` int(11) NOT NULL,
  `urls_id` int(11) NOT NULL,
  `title` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `size` int(11) NOT NULL,
  `load_time` int(11) NOT NULL COMMENT '(in seconds)',
  `response_code` int(11) NOT NULL,
  `doc_type` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `server_info` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cr_crawled_pages`
--

LOCK TABLES `cr_crawled_pages` WRITE;
/*!40000 ALTER TABLE `cr_crawled_pages` DISABLE KEYS */;
/*!40000 ALTER TABLE `cr_crawled_pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cr_crawled_pages_metas`
--

DROP TABLE IF EXISTS `cr_crawled_pages_metas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cr_crawled_pages_metas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `crawled_pages_id` int(11) NOT NULL,
  `key` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cr_crawled_pages_metas`
--

LOCK TABLES `cr_crawled_pages_metas` WRITE;
/*!40000 ALTER TABLE `cr_crawled_pages_metas` DISABLE KEYS */;
/*!40000 ALTER TABLE `cr_crawled_pages_metas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cr_errors`
--

DROP TABLE IF EXISTS `cr_errors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cr_errors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `crawled_pages_id` int(11) NOT NULL,
  `recheck_crawls_id` int(11) NOT NULL,
  `error_code` int(11) NOT NULL,
  `details` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cr_errors`
--

LOCK TABLES `cr_errors` WRITE;
/*!40000 ALTER TABLE `cr_errors` DISABLE KEYS */;
/*!40000 ALTER TABLE `cr_errors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cr_headings`
--

DROP TABLE IF EXISTS `cr_headings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cr_headings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level` int(11) NOT NULL,
  `instance` int(11) NOT NULL,
  `crawled_pages_id` int(11) NOT NULL,
  `raw_heading` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `a_tags_id` int(11) NOT NULL,
  `heading_text` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cr_headings`
--

LOCK TABLES `cr_headings` WRITE;
/*!40000 ALTER TABLE `cr_headings` DISABLE KEYS */;
/*!40000 ALTER TABLE `cr_headings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cr_imgs`
--

DROP TABLE IF EXISTS `cr_imgs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cr_imgs` (
  `img_id` int(11) NOT NULL AUTO_INCREMENT,
  `url_id` int(11) NOT NULL,
  `raw_src` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `raw_img` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `page_id` int(11) NOT NULL,
  PRIMARY KEY (`img_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cr_imgs`
--

LOCK TABLES `cr_imgs` WRITE;
/*!40000 ALTER TABLE `cr_imgs` DISABLE KEYS */;
/*!40000 ALTER TABLE `cr_imgs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cr_imgs_metas`
--

DROP TABLE IF EXISTS `cr_imgs_metas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cr_imgs_metas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `imgs_id` int(11) NOT NULL,
  `key` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cr_imgs_metas`
--

LOCK TABLES `cr_imgs_metas` WRITE;
/*!40000 ALTER TABLE `cr_imgs_metas` DISABLE KEYS */;
/*!40000 ALTER TABLE `cr_imgs_metas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cr_keywords`
--

DROP TABLE IF EXISTS `cr_keywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cr_keywords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `campaigns_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cr_keywords`
--

LOCK TABLES `cr_keywords` WRITE;
/*!40000 ALTER TABLE `cr_keywords` DISABLE KEYS */;
/*!40000 ALTER TABLE `cr_keywords` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cr_link_tags`
--

DROP TABLE IF EXISTS `cr_link_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cr_link_tags` (
  `link_tag_id` int(11) NOT NULL AUTO_INCREMENT,
  `raw_link_tag` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `page_id` int(11) NOT NULL,
  `referer_page_id` int(11) NOT NULL,
  PRIMARY KEY (`link_tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cr_link_tags`
--

LOCK TABLES `cr_link_tags` WRITE;
/*!40000 ALTER TABLE `cr_link_tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `cr_link_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cr_link_tags_metas`
--

DROP TABLE IF EXISTS `cr_link_tags_metas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cr_link_tags_metas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link_tags_id` int(11) NOT NULL,
  `key` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cr_link_tags_metas`
--

LOCK TABLES `cr_link_tags_metas` WRITE;
/*!40000 ALTER TABLE `cr_link_tags_metas` DISABLE KEYS */;
/*!40000 ALTER TABLE `cr_link_tags_metas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cr_prominent_keywords`
--

DROP TABLE IF EXISTS `cr_prominent_keywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cr_prominent_keywords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `crawled_pages_id` int(11) DEFAULT NULL,
  `plain_text` text,
  `prominent_keywords_array` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cr_prominent_keywords`
--

LOCK TABLES `cr_prominent_keywords` WRITE;
/*!40000 ALTER TABLE `cr_prominent_keywords` DISABLE KEYS */;
/*!40000 ALTER TABLE `cr_prominent_keywords` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cr_tmp_urls`
--

DROP TABLE IF EXISTS `cr_tmp_urls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cr_tmp_urls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cr_tmp_urls`
--

LOCK TABLES `cr_tmp_urls` WRITE;
/*!40000 ALTER TABLE `cr_tmp_urls` DISABLE KEYS */;
/*!40000 ALTER TABLE `cr_tmp_urls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cr_urls`
--

DROP TABLE IF EXISTS `cr_urls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cr_urls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rebuilt_url` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rebuilt_url` (`rebuilt_url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cr_urls`
--

LOCK TABLES `cr_urls` WRITE;
/*!40000 ALTER TABLE `cr_urls` DISABLE KEYS */;
/*!40000 ALTER TABLE `cr_urls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cr_users`
--

DROP TABLE IF EXISTS `cr_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cr_users` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `email` varchar(120) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `user_type_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cr_users`
--

LOCK TABLES `cr_users` WRITE;
/*!40000 ALTER TABLE `cr_users` DISABLE KEYS */;
INSERT INTO `cr_users` VALUES (1,'joe','joe','',0,NULL,NULL,NULL);
/*!40000 ALTER TABLE `cr_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cr_users_types`
--

DROP TABLE IF EXISTS `cr_users_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cr_users_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cr_users_types`
--

LOCK TABLES `cr_users_types` WRITE;
/*!40000 ALTER TABLE `cr_users_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `cr_users_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `google_analytics_accounts`
--

DROP TABLE IF EXISTS `google_analytics_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `google_analytics_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datasource` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Passwd` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `google_analytics_accounts`
--

LOCK TABLES `google_analytics_accounts` WRITE;
/*!40000 ALTER TABLE `google_analytics_accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `google_analytics_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `so_answers`
--

DROP TABLE IF EXISTS `so_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `so_answers` (
  `answer_id` int(11) NOT NULL AUTO_INCREMENT,
  `answer` text NOT NULL,
  `question_id` int(11) NOT NULL,
  `siteopt_id` int(11) NOT NULL,
  PRIMARY KEY (`answer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `so_answers`
--

LOCK TABLES `so_answers` WRITE;
/*!40000 ALTER TABLE `so_answers` DISABLE KEYS */;
/*!40000 ALTER TABLE `so_answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `so_cms`
--

DROP TABLE IF EXISTS `so_cms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `so_cms` (
  `cms_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `current_version` decimal(10,3) NOT NULL,
  PRIMARY KEY (`cms_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `so_cms`
--

LOCK TABLES `so_cms` WRITE;
/*!40000 ALTER TABLE `so_cms` DISABLE KEYS */;
/*!40000 ALTER TABLE `so_cms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `so_default_answers`
--

DROP TABLE IF EXISTS `so_default_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `so_default_answers` (
  `default_answer_id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `default_answer_item` text NOT NULL,
  `cms_id` int(11) NOT NULL,
  PRIMARY KEY (`default_answer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `so_default_answers`
--

LOCK TABLES `so_default_answers` WRITE;
/*!40000 ALTER TABLE `so_default_answers` DISABLE KEYS */;
/*!40000 ALTER TABLE `so_default_answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `so_dependencies`
--

DROP TABLE IF EXISTS `so_dependencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `so_dependencies` (
  `dep_id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `dep_on_question_id` int(11) NOT NULL,
  `show_if_default_answer_id` int(11) NOT NULL,
  PRIMARY KEY (`dep_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `so_dependencies`
--

LOCK TABLES `so_dependencies` WRITE;
/*!40000 ALTER TABLE `so_dependencies` DISABLE KEYS */;
/*!40000 ALTER TABLE `so_dependencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `so_question_groups`
--

DROP TABLE IF EXISTS `so_question_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `so_question_groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `version_id` int(11) NOT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `so_question_groups`
--

LOCK TABLES `so_question_groups` WRITE;
/*!40000 ALTER TABLE `so_question_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `so_question_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `so_question_types`
--

DROP TABLE IF EXISTS `so_question_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `so_question_types` (
  `question_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL,
  PRIMARY KEY (`question_type_id`),
  UNIQUE KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `so_question_types`
--

LOCK TABLES `so_question_types` WRITE;
/*!40000 ALTER TABLE `so_question_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `so_question_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `so_questions`
--

DROP TABLE IF EXISTS `so_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `so_questions` (
  `question_id` int(11) NOT NULL AUTO_INCREMENT,
  `question` text NOT NULL,
  `group_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `version_id` int(11) NOT NULL,
  `field_order` int(11) NOT NULL,
  `question_type_id` int(11) NOT NULL,
  PRIMARY KEY (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `so_questions`
--

LOCK TABLES `so_questions` WRITE;
/*!40000 ALTER TABLE `so_questions` DISABLE KEYS */;
/*!40000 ALTER TABLE `so_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `so_siteopts`
--

DROP TABLE IF EXISTS `so_siteopts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `so_siteopts` (
  `siteopt_id` int(11) NOT NULL AUTO_INCREMENT,
  `crawl_id` int(11) NOT NULL,
  `version_id` int(11) NOT NULL,
  `cms_id` int(11) NOT NULL,
  PRIMARY KEY (`siteopt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `so_siteopts`
--

LOCK TABLES `so_siteopts` WRITE;
/*!40000 ALTER TABLE `so_siteopts` DISABLE KEYS */;
/*!40000 ALTER TABLE `so_siteopts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `so_sn_codes`
--

DROP TABLE IF EXISTS `so_sn_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `so_sn_codes` (
  `sn_code_id` int(11) NOT NULL AUTO_INCREMENT,
  `code_snippet` text NOT NULL,
  `sn_id` int(11) NOT NULL,
  `purpose` varchar(50) NOT NULL,
  PRIMARY KEY (`sn_code_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `so_sn_codes`
--

LOCK TABLES `so_sn_codes` WRITE;
/*!40000 ALTER TABLE `so_sn_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `so_sn_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `so_social_networks`
--

DROP TABLE IF EXISTS `so_social_networks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `so_social_networks` (
  `sn_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `url` varchar(100) NOT NULL,
  PRIMARY KEY (`sn_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `so_social_networks`
--

LOCK TABLES `so_social_networks` WRITE;
/*!40000 ALTER TABLE `so_social_networks` DISABLE KEYS */;
/*!40000 ALTER TABLE `so_social_networks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `so_versions`
--

DROP TABLE IF EXISTS `so_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `so_versions` (
  `version_id` int(11) NOT NULL AUTO_INCREMENT,
  `version` decimal(10,1) NOT NULL,
  PRIMARY KEY (`version_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `so_versions`
--

LOCK TABLES `so_versions` WRITE;
/*!40000 ALTER TABLE `so_versions` DISABLE KEYS */;
INSERT INTO `so_versions` VALUES (1,0.1);
/*!40000 ALTER TABLE `so_versions` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-01-17 10:19:49

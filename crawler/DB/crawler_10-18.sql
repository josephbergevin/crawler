-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Oct 18, 2012 at 02:49 PM
-- Server version: 5.1.44
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `crawler`
--

-- --------------------------------------------------------

--
-- Table structure for table `cr_anchors`
--

CREATE TABLE IF NOT EXISTS `cr_anchors` (
  `anchor_id` int(11) NOT NULL AUTO_INCREMENT,
  `trimmed_anchor` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`anchor_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=954 ;

-- --------------------------------------------------------

--
-- Table structure for table `cr_anchors_2`
--

CREATE TABLE IF NOT EXISTS `cr_anchors_2` (
  `anchor_id` int(11) NOT NULL AUTO_INCREMENT,
  `trimmed_anchor` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`anchor_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=954 ;

-- --------------------------------------------------------

--
-- Table structure for table `cr_a_tags`
--

CREATE TABLE IF NOT EXISTS `cr_a_tags` (
  `a_tag_id` int(11) NOT NULL AUTO_INCREMENT,
  `url_id` int(11) NOT NULL,
  `raw_href` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `relative` int(11) NOT NULL COMMENT '0=false; 1=true',
  `anchor_id` int(11) NOT NULL,
  `raw_anchor` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `img_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  PRIMARY KEY (`a_tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=60411 ;

-- --------------------------------------------------------

--
-- Table structure for table `cr_a_tag_metas`
--

CREATE TABLE IF NOT EXISTS `cr_a_tag_metas` (
  `a_tag_meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `a_tag_id` int(11) NOT NULL,
  `key` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`a_tag_meta_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=62108 ;

-- --------------------------------------------------------

--
-- Table structure for table `cr_campaigns`
--

CREATE TABLE IF NOT EXISTS `cr_campaigns` (
  `campaign_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `crawl_rules` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cr_cookies`
--

CREATE TABLE IF NOT EXISTS `cr_cookies` (
  `cookie_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`cookie_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cr_errors`
--

CREATE TABLE IF NOT EXISTS `cr_errors` (
  `error_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `recheck_crawl_id` int(11) NOT NULL,
  `error_code` int(11) NOT NULL,
  `details` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`error_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cr_headings`
--

CREATE TABLE IF NOT EXISTS `cr_headings` (
  `header_id` int(11) NOT NULL AUTO_INCREMENT,
  `level` int(11) NOT NULL,
  `instance` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `raw_header` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `a_tag_id` int(11) NOT NULL,
  `header_text` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`header_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5401 ;

-- --------------------------------------------------------

--
-- Table structure for table `cr_imgs`
--

CREATE TABLE IF NOT EXISTS `cr_imgs` (
  `img_id` int(11) NOT NULL AUTO_INCREMENT,
  `url_id` int(11) NOT NULL,
  `raw_src` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `raw_img` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `page_id` int(11) NOT NULL,
  PRIMARY KEY (`img_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12630 ;

-- --------------------------------------------------------

--
-- Table structure for table `cr_img_metas`
--

CREATE TABLE IF NOT EXISTS `cr_img_metas` (
  `img_meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `img_id` int(11) NOT NULL,
  `key` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`img_meta_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=23293 ;

-- --------------------------------------------------------

--
-- Table structure for table `cr_keywords`
--

CREATE TABLE IF NOT EXISTS `cr_keywords` (
  `keyword_id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `campaign_id` int(11) NOT NULL,
  PRIMARY KEY (`keyword_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cr_link_tags`
--

CREATE TABLE IF NOT EXISTS `cr_link_tags` (
  `link_tag_id` int(11) NOT NULL AUTO_INCREMENT,
  `raw_link_tag` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `page_id` int(11) NOT NULL,
  `referer_page_id` int(11) NOT NULL,
  PRIMARY KEY (`link_tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1421 ;

-- --------------------------------------------------------

--
-- Table structure for table `cr_link_tag_metas`
--

CREATE TABLE IF NOT EXISTS `cr_link_tag_metas` (
  `link_tag_meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `link_tag_id` int(11) NOT NULL,
  `key` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`link_tag_meta_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3290 ;

-- --------------------------------------------------------

--
-- Table structure for table `cr_pages`
--

CREATE TABLE IF NOT EXISTS `cr_pages` (
  `page_id` int(11) NOT NULL AUTO_INCREMENT,
  `crawl_id` int(11) NOT NULL,
  `url_id` int(11) NOT NULL,
  `title` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `size` int(11) NOT NULL,
  `load_time` int(11) NOT NULL COMMENT '(in seconds)',
  `response_code` int(11) NOT NULL,
  `doc_type` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `server_info` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`page_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=558 ;

-- --------------------------------------------------------

--
-- Table structure for table `cr_page_metas`
--

CREATE TABLE IF NOT EXISTS `cr_page_metas` (
  `page_meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `key` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`page_meta_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cr_tmp_urls`
--

CREATE TABLE IF NOT EXISTS `cr_tmp_urls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cr_urls`
--

CREATE TABLE IF NOT EXISTS `cr_urls` (
  `url_id` int(11) NOT NULL AUTO_INCREMENT,
  `rebuilt_url` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`url_id`),
  UNIQUE KEY `rebuilt_url` (`rebuilt_url`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3037 ;

-- --------------------------------------------------------

--
-- Table structure for table `so_answers`
--

CREATE TABLE IF NOT EXISTS `so_answers` (
  `answer_id` int(11) NOT NULL AUTO_INCREMENT,
  `answer` text NOT NULL,
  `question_id` int(11) NOT NULL,
  `siteopt_id` int(11) NOT NULL,
  PRIMARY KEY (`answer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `so_cms`
--

CREATE TABLE IF NOT EXISTS `so_cms` (
  `cms_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `current_version` decimal(10,3) NOT NULL,
  PRIMARY KEY (`cms_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `so_default_answers`
--

CREATE TABLE IF NOT EXISTS `so_default_answers` (
  `default_answer_id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `default_answer_item` text NOT NULL,
  `cms_id` int(11) NOT NULL,
  PRIMARY KEY (`default_answer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `so_dependencies`
--

CREATE TABLE IF NOT EXISTS `so_dependencies` (
  `dep_id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `dep_on_question_id` int(11) NOT NULL,
  `show_if_default_answer_id` int(11) NOT NULL,
  PRIMARY KEY (`dep_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `so_questions`
--

CREATE TABLE IF NOT EXISTS `so_questions` (
  `question_id` int(11) NOT NULL AUTO_INCREMENT,
  `question` text NOT NULL,
  `group_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `version_id` int(11) NOT NULL,
  `field_order` int(11) NOT NULL,
  `question_type_id` int(11) NOT NULL,
  PRIMARY KEY (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `so_question_groups`
--

CREATE TABLE IF NOT EXISTS `so_question_groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `version_id` int(11) NOT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `so_question_types`
--

CREATE TABLE IF NOT EXISTS `so_question_types` (
  `question_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL,
  PRIMARY KEY (`question_type_id`),
  UNIQUE KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `so_siteopts`
--

CREATE TABLE IF NOT EXISTS `so_siteopts` (
  `siteopt_id` int(11) NOT NULL AUTO_INCREMENT,
  `crawl_id` int(11) NOT NULL,
  `version_id` int(11) NOT NULL,
  `cms_id` int(11) NOT NULL,
  PRIMARY KEY (`siteopt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `so_sn_codes`
--

CREATE TABLE IF NOT EXISTS `so_sn_codes` (
  `sn_code_id` int(11) NOT NULL AUTO_INCREMENT,
  `code_snippet` text NOT NULL,
  `sn_id` int(11) NOT NULL,
  `purpose` varchar(50) NOT NULL,
  PRIMARY KEY (`sn_code_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `so_social_networks`
--

CREATE TABLE IF NOT EXISTS `so_social_networks` (
  `sn_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `url` varchar(100) NOT NULL,
  PRIMARY KEY (`sn_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `so_versions`
--

CREATE TABLE IF NOT EXISTS `so_versions` (
  `version_id` int(11) NOT NULL AUTO_INCREMENT,
  `version` decimal(10,1) NOT NULL,
  PRIMARY KEY (`version_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

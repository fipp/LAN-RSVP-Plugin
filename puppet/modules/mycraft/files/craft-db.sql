-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 19, 2016 at 08:13 AM
-- Server version: 5.5.47-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `craft`
--

-- --------------------------------------------------------

--
-- Table structure for table `craft_assetfiles`
--

DROP TABLE IF EXISTS `craft_assetfiles`;
CREATE TABLE IF NOT EXISTS `craft_assetfiles` (
  `id` int(11) NOT NULL,
  `sourceId` int(11) DEFAULT NULL,
  `folderId` int(11) NOT NULL,
  `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `kind` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'unknown',
  `width` int(11) unsigned DEFAULT NULL,
  `height` int(11) unsigned DEFAULT NULL,
  `size` bigint(20) unsigned DEFAULT NULL,
  `dateModified` datetime DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_assetfiles_filename_folderId_unq_idx` (`filename`,`folderId`),
  KEY `craft_assetfiles_sourceId_fk` (`sourceId`),
  KEY `craft_assetfiles_folderId_fk` (`folderId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `craft_assetfolders`
--

DROP TABLE IF EXISTS `craft_assetfolders`;
CREATE TABLE IF NOT EXISTS `craft_assetfolders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parentId` int(11) DEFAULT NULL,
  `sourceId` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_assetfolders_name_parentId_sourceId_unq_idx` (`name`,`parentId`,`sourceId`),
  KEY `craft_assetfolders_parentId_fk` (`parentId`),
  KEY `craft_assetfolders_sourceId_fk` (`sourceId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `craft_assetindexdata`
--

DROP TABLE IF EXISTS `craft_assetindexdata`;
CREATE TABLE IF NOT EXISTS `craft_assetindexdata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sessionId` varchar(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `sourceId` int(10) NOT NULL,
  `offset` int(10) NOT NULL,
  `uri` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `size` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `recordId` int(10) DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_assetindexdata_sessionId_sourceId_offset_unq_idx` (`sessionId`,`sourceId`,`offset`),
  KEY `craft_assetindexdata_sourceId_fk` (`sourceId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `craft_assetsources`
--

DROP TABLE IF EXISTS `craft_assetsources`;
CREATE TABLE IF NOT EXISTS `craft_assetsources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `handle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `settings` text COLLATE utf8_unicode_ci,
  `sortOrder` smallint(6) unsigned DEFAULT NULL,
  `fieldLayoutId` int(10) DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_assetsources_name_unq_idx` (`name`),
  UNIQUE KEY `craft_assetsources_handle_unq_idx` (`handle`),
  KEY `craft_assetsources_fieldLayoutId_fk` (`fieldLayoutId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `craft_assettransformindex`
--

DROP TABLE IF EXISTS `craft_assettransformindex`;
CREATE TABLE IF NOT EXISTS `craft_assettransformindex` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fileId` int(11) NOT NULL,
  `filename` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `format` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sourceId` int(11) DEFAULT NULL,
  `fileExists` tinyint(1) DEFAULT NULL,
  `inProgress` tinyint(1) DEFAULT NULL,
  `dateIndexed` datetime DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `craft_assettransformindex_sourceId_fileId_location_idx` (`sourceId`,`fileId`,`location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `craft_assettransforms`
--

DROP TABLE IF EXISTS `craft_assettransforms`;
CREATE TABLE IF NOT EXISTS `craft_assettransforms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `handle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mode` enum('stretch','fit','crop') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'crop',
  `position` enum('top-left','top-center','top-right','center-left','center-center','center-right','bottom-left','bottom-center','bottom-right') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'center-center',
  `height` int(10) DEFAULT NULL,
  `width` int(10) DEFAULT NULL,
  `format` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `quality` int(10) DEFAULT NULL,
  `dimensionChangeTime` datetime DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_assettransforms_name_unq_idx` (`name`),
  UNIQUE KEY `craft_assettransforms_handle_unq_idx` (`handle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `craft_categories`
--

DROP TABLE IF EXISTS `craft_categories`;
CREATE TABLE IF NOT EXISTS `craft_categories` (
  `id` int(11) NOT NULL,
  `groupId` int(11) NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `craft_categories_groupId_fk` (`groupId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `craft_categorygroups`
--

DROP TABLE IF EXISTS `craft_categorygroups`;
CREATE TABLE IF NOT EXISTS `craft_categorygroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `structureId` int(11) NOT NULL,
  `fieldLayoutId` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `handle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `hasUrls` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `template` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_categorygroups_name_unq_idx` (`name`),
  UNIQUE KEY `craft_categorygroups_handle_unq_idx` (`handle`),
  KEY `craft_categorygroups_structureId_fk` (`structureId`),
  KEY `craft_categorygroups_fieldLayoutId_fk` (`fieldLayoutId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `craft_categorygroups_i18n`
--

DROP TABLE IF EXISTS `craft_categorygroups_i18n`;
CREATE TABLE IF NOT EXISTS `craft_categorygroups_i18n` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupId` int(11) NOT NULL,
  `locale` char(12) COLLATE utf8_unicode_ci NOT NULL,
  `urlFormat` text COLLATE utf8_unicode_ci,
  `nestedUrlFormat` text COLLATE utf8_unicode_ci,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_categorygroups_i18n_groupId_locale_unq_idx` (`groupId`,`locale`),
  KEY `craft_categorygroups_i18n_locale_fk` (`locale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `craft_content`
--

DROP TABLE IF EXISTS `craft_content`;
CREATE TABLE IF NOT EXISTS `craft_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `elementId` int(11) NOT NULL,
  `locale` char(12) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `field_body` text COLLATE utf8_unicode_ci,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_content_elementId_locale_unq_idx` (`elementId`,`locale`),
  KEY `craft_content_title_idx` (`title`),
  KEY `craft_content_locale_fk` (`locale`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `craft_content`
--

INSERT INTO `craft_content` (`id`, `elementId`, `locale`, `title`, `field_body`, `dateCreated`, `dateUpdated`, `uid`) VALUES
(1, 1, 'en_us', NULL, NULL, '2016-04-19 08:12:56', '2016-04-19 08:12:56', '1b330e2d-df23-43de-a93e-d20d04266304'),
(2, 2, 'en_us', 'Welcome to Craft.seatmapevents.dev!', '<p>It’s true, this site doesn’t have a whole lot of content yet, but don’t worry. Our web developers have just installed the CMS, and they’re setting things up for the content editors this very moment. Soon Craft.seatmapevents.dev will be an oasis of fresh perspectives, sharp analyses, and astute opinions that will keep you coming back again and again.</p>', '2016-04-19 08:12:57', '2016-04-19 08:12:57', 'c050a05a-5d34-45e0-a320-678762ce2b1f'),
(3, 3, 'en_us', 'We just installed Craft!', '<p>Craft is the CMS that’s powering Craft.seatmapevents.dev. It’s beautiful, powerful, flexible, and easy-to-use, and it’s made by Pixel &amp; Tonic. We can’t wait to dive in and see what it’s capable of!</p><!--pagebreak--><p>This is even more captivating content, which you couldn’t see on the News index page because it was entered after a Page Break, and the News index template only likes to show the content on the first page.</p><p>Craft: a nice alternative to Word, if you’re making a website.</p>', '2016-04-19 08:12:57', '2016-04-19 08:12:57', '092b15ae-5db1-4af9-b961-5db6a9be610c');

-- --------------------------------------------------------

--
-- Table structure for table `craft_deprecationerrors`
--

DROP TABLE IF EXISTS `craft_deprecationerrors`;
CREATE TABLE IF NOT EXISTS `craft_deprecationerrors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fingerprint` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lastOccurrence` datetime NOT NULL,
  `file` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `line` smallint(6) unsigned NOT NULL,
  `class` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `method` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `template` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `templateLine` smallint(6) unsigned DEFAULT NULL,
  `message` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `traces` text COLLATE utf8_unicode_ci,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_deprecationerrors_key_fingerprint_unq_idx` (`key`,`fingerprint`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `craft_elementindexsettings`
--

DROP TABLE IF EXISTS `craft_elementindexsettings`;
CREATE TABLE IF NOT EXISTS `craft_elementindexsettings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `settings` text COLLATE utf8_unicode_ci,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_elementindexsettings_type_unq_idx` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `craft_elements`
--

DROP TABLE IF EXISTS `craft_elements`;
CREATE TABLE IF NOT EXISTS `craft_elements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `archived` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `craft_elements_type_idx` (`type`),
  KEY `craft_elements_enabled_idx` (`enabled`),
  KEY `craft_elements_archived_dateCreated_idx` (`archived`,`dateCreated`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `craft_elements`
--

INSERT INTO `craft_elements` (`id`, `type`, `enabled`, `archived`, `dateCreated`, `dateUpdated`, `uid`) VALUES
(1, 'User', 1, 0, '2016-04-19 08:12:56', '2016-04-19 08:12:56', '9b64043d-4392-4697-8279-4155f9c74c9c'),
(2, 'Entry', 1, 0, '2016-04-19 08:12:57', '2016-04-19 08:12:57', '954087ee-5519-4f55-8a41-3d974f540af3'),
(3, 'Entry', 1, 0, '2016-04-19 08:12:57', '2016-04-19 08:12:57', '5fb74bb3-5998-4919-a235-eaebb01082f2');

-- --------------------------------------------------------

--
-- Table structure for table `craft_elements_i18n`
--

DROP TABLE IF EXISTS `craft_elements_i18n`;
CREATE TABLE IF NOT EXISTS `craft_elements_i18n` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `elementId` int(11) NOT NULL,
  `locale` char(12) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `uri` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_elements_i18n_elementId_locale_unq_idx` (`elementId`,`locale`),
  UNIQUE KEY `craft_elements_i18n_uri_locale_unq_idx` (`uri`,`locale`),
  KEY `craft_elements_i18n_slug_locale_idx` (`slug`,`locale`),
  KEY `craft_elements_i18n_enabled_idx` (`enabled`),
  KEY `craft_elements_i18n_locale_fk` (`locale`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `craft_elements_i18n`
--

INSERT INTO `craft_elements_i18n` (`id`, `elementId`, `locale`, `slug`, `uri`, `enabled`, `dateCreated`, `dateUpdated`, `uid`) VALUES
(1, 1, 'en_us', '', NULL, 1, '2016-04-19 08:12:56', '2016-04-19 08:12:56', 'c594b5a8-19d6-4dd7-806e-9a3051733ba9'),
(2, 2, 'en_us', 'homepage', '__home__', 1, '2016-04-19 08:12:57', '2016-04-19 08:12:57', '8d0d667c-5cc0-41c8-ae6b-b9f9693e1fad'),
(3, 3, 'en_us', 'we-just-installed-craft', 'news/2016/we-just-installed-craft', 1, '2016-04-19 08:12:57', '2016-04-19 08:12:57', 'd8b8f459-de4c-472b-84c7-2b6c76eabd8d');

-- --------------------------------------------------------

--
-- Table structure for table `craft_emailmessages`
--

DROP TABLE IF EXISTS `craft_emailmessages`;
CREATE TABLE IF NOT EXISTS `craft_emailmessages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` char(150) COLLATE utf8_unicode_ci NOT NULL,
  `locale` char(12) COLLATE utf8_unicode_ci NOT NULL,
  `subject` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_emailmessages_key_locale_unq_idx` (`key`,`locale`),
  KEY `craft_emailmessages_locale_fk` (`locale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `craft_entries`
--

DROP TABLE IF EXISTS `craft_entries`;
CREATE TABLE IF NOT EXISTS `craft_entries` (
  `id` int(11) NOT NULL,
  `sectionId` int(11) NOT NULL,
  `typeId` int(11) DEFAULT NULL,
  `authorId` int(11) DEFAULT NULL,
  `postDate` datetime DEFAULT NULL,
  `expiryDate` datetime DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `craft_entries_sectionId_idx` (`sectionId`),
  KEY `craft_entries_typeId_idx` (`typeId`),
  KEY `craft_entries_postDate_idx` (`postDate`),
  KEY `craft_entries_expiryDate_idx` (`expiryDate`),
  KEY `craft_entries_authorId_fk` (`authorId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `craft_entries`
--

INSERT INTO `craft_entries` (`id`, `sectionId`, `typeId`, `authorId`, `postDate`, `expiryDate`, `dateCreated`, `dateUpdated`, `uid`) VALUES
(2, 1, NULL, NULL, '2016-04-19 08:12:57', NULL, '2016-04-19 08:12:57', '2016-04-19 08:12:57', 'ca7bd4e7-baf5-4e5f-864d-179c57862c88'),
(3, 2, 2, 1, '2016-04-19 08:12:57', NULL, '2016-04-19 08:12:57', '2016-04-19 08:12:57', '87979d49-acce-4439-80c6-2a248020cdf2');

-- --------------------------------------------------------

--
-- Table structure for table `craft_entrydrafts`
--

DROP TABLE IF EXISTS `craft_entrydrafts`;
CREATE TABLE IF NOT EXISTS `craft_entrydrafts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entryId` int(11) NOT NULL,
  `sectionId` int(11) NOT NULL,
  `creatorId` int(11) NOT NULL,
  `locale` char(12) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `notes` tinytext COLLATE utf8_unicode_ci,
  `data` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `craft_entrydrafts_entryId_locale_idx` (`entryId`,`locale`),
  KEY `craft_entrydrafts_sectionId_fk` (`sectionId`),
  KEY `craft_entrydrafts_creatorId_fk` (`creatorId`),
  KEY `craft_entrydrafts_locale_fk` (`locale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `craft_entrytypes`
--

DROP TABLE IF EXISTS `craft_entrytypes`;
CREATE TABLE IF NOT EXISTS `craft_entrytypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sectionId` int(11) NOT NULL,
  `fieldLayoutId` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `handle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `hasTitleField` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `titleLabel` varchar(255) COLLATE utf8_unicode_ci DEFAULT 'Title',
  `titleFormat` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sortOrder` smallint(6) unsigned DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_entrytypes_name_sectionId_unq_idx` (`name`,`sectionId`),
  UNIQUE KEY `craft_entrytypes_handle_sectionId_unq_idx` (`handle`,`sectionId`),
  KEY `craft_entrytypes_sectionId_fk` (`sectionId`),
  KEY `craft_entrytypes_fieldLayoutId_fk` (`fieldLayoutId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `craft_entrytypes`
--

INSERT INTO `craft_entrytypes` (`id`, `sectionId`, `fieldLayoutId`, `name`, `handle`, `hasTitleField`, `titleLabel`, `titleFormat`, `sortOrder`, `dateCreated`, `dateUpdated`, `uid`) VALUES
(1, 1, 3, 'Homepage', 'homepage', 1, 'Title', NULL, NULL, '2016-04-19 08:12:57', '2016-04-19 08:12:57', '8db88095-b08e-44a3-9e31-01dcc2e926ef'),
(2, 2, 5, 'News', 'news', 1, 'Title', NULL, NULL, '2016-04-19 08:12:57', '2016-04-19 08:12:57', '34ae40a9-7c36-4648-9370-778c3ff0eafc');

-- --------------------------------------------------------

--
-- Table structure for table `craft_entryversions`
--

DROP TABLE IF EXISTS `craft_entryversions`;
CREATE TABLE IF NOT EXISTS `craft_entryversions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entryId` int(11) NOT NULL,
  `sectionId` int(11) NOT NULL,
  `creatorId` int(11) DEFAULT NULL,
  `locale` char(12) COLLATE utf8_unicode_ci NOT NULL,
  `num` smallint(6) unsigned NOT NULL,
  `notes` tinytext COLLATE utf8_unicode_ci,
  `data` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `craft_entryversions_entryId_locale_idx` (`entryId`,`locale`),
  KEY `craft_entryversions_sectionId_fk` (`sectionId`),
  KEY `craft_entryversions_creatorId_fk` (`creatorId`),
  KEY `craft_entryversions_locale_fk` (`locale`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `craft_entryversions`
--

INSERT INTO `craft_entryversions` (`id`, `entryId`, `sectionId`, `creatorId`, `locale`, `num`, `notes`, `data`, `dateCreated`, `dateUpdated`, `uid`) VALUES
(1, 2, 1, 1, 'en_us', 1, NULL, '{"typeId":"1","authorId":null,"title":"Homepage","slug":"homepage","postDate":1461053577,"expiryDate":null,"enabled":1,"parentId":null,"fields":[]}', '2016-04-19 08:12:57', '2016-04-19 08:12:57', '672958ea-1a77-48b3-aa23-33b3ee849256'),
(2, 2, 1, 1, 'en_us', 2, NULL, '{"typeId":null,"authorId":null,"title":"Welcome to Craft.seatmapevents.dev!","slug":"homepage","postDate":1461053577,"expiryDate":null,"enabled":"1","parentId":null,"fields":{"1":"<p>It\\u2019s true, this site doesn\\u2019t have a whole lot of content yet, but don\\u2019t worry. Our web developers have just installed the CMS, and they\\u2019re setting things up for the content editors this very moment. Soon Craft.seatmapevents.dev will be an oasis of fresh perspectives, sharp analyses, and astute opinions that will keep you coming back again and again.<\\/p>"}}', '2016-04-19 08:12:57', '2016-04-19 08:12:57', '2a1c82f1-2d19-4fdc-b812-e21400ebcb1c'),
(3, 3, 2, 1, 'en_us', 1, NULL, '{"typeId":"2","authorId":"1","title":"We just installed Craft!","slug":"we-just-installed-craft","postDate":1461053577,"expiryDate":null,"enabled":1,"parentId":null,"fields":[]}', '2016-04-19 08:12:57', '2016-04-19 08:12:57', '1fc28e37-fc68-43f9-a5fc-5195248bf816');

-- --------------------------------------------------------

--
-- Table structure for table `craft_fieldgroups`
--

DROP TABLE IF EXISTS `craft_fieldgroups`;
CREATE TABLE IF NOT EXISTS `craft_fieldgroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_fieldgroups_name_unq_idx` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `craft_fieldgroups`
--

INSERT INTO `craft_fieldgroups` (`id`, `name`, `dateCreated`, `dateUpdated`, `uid`) VALUES
(1, 'Default', '2016-04-19 08:12:57', '2016-04-19 08:12:57', '188003ca-e75e-4963-ab82-1ceb1a5976bf');

-- --------------------------------------------------------

--
-- Table structure for table `craft_fieldlayoutfields`
--

DROP TABLE IF EXISTS `craft_fieldlayoutfields`;
CREATE TABLE IF NOT EXISTS `craft_fieldlayoutfields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `layoutId` int(11) NOT NULL,
  `tabId` int(11) NOT NULL,
  `fieldId` int(11) NOT NULL,
  `required` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sortOrder` smallint(6) unsigned DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_fieldlayoutfields_layoutId_fieldId_unq_idx` (`layoutId`,`fieldId`),
  KEY `craft_fieldlayoutfields_sortOrder_idx` (`sortOrder`),
  KEY `craft_fieldlayoutfields_tabId_fk` (`tabId`),
  KEY `craft_fieldlayoutfields_fieldId_fk` (`fieldId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `craft_fieldlayoutfields`
--

INSERT INTO `craft_fieldlayoutfields` (`id`, `layoutId`, `tabId`, `fieldId`, `required`, `sortOrder`, `dateCreated`, `dateUpdated`, `uid`) VALUES
(1, 3, 1, 1, 1, 1, '2016-04-19 08:12:57', '2016-04-19 08:12:57', '755ca38f-20e0-4198-99f1-b757b4442d3a'),
(2, 5, 2, 1, 1, 1, '2016-04-19 08:12:57', '2016-04-19 08:12:57', '9909b8ac-27c0-4b10-9134-34fb6978fd34'),
(3, 5, 2, 2, 0, 2, '2016-04-19 08:12:57', '2016-04-19 08:12:57', 'dcf9a2e8-bdbe-4cab-a552-a44590994fe1');

-- --------------------------------------------------------

--
-- Table structure for table `craft_fieldlayouts`
--

DROP TABLE IF EXISTS `craft_fieldlayouts`;
CREATE TABLE IF NOT EXISTS `craft_fieldlayouts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `craft_fieldlayouts_type_idx` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `craft_fieldlayouts`
--

INSERT INTO `craft_fieldlayouts` (`id`, `type`, `dateCreated`, `dateUpdated`, `uid`) VALUES
(1, 'Tag', '2016-04-19 08:12:57', '2016-04-19 08:12:57', 'afb0e76b-88d1-47f9-a936-f47ca1c5472e'),
(3, 'Entry', '2016-04-19 08:12:57', '2016-04-19 08:12:57', 'f0b2179e-88e1-4255-b030-26f375795eb7'),
(5, 'Entry', '2016-04-19 08:12:57', '2016-04-19 08:12:57', '4fb67681-70b2-4b14-ac06-2d043d82f60d');

-- --------------------------------------------------------

--
-- Table structure for table `craft_fieldlayouttabs`
--

DROP TABLE IF EXISTS `craft_fieldlayouttabs`;
CREATE TABLE IF NOT EXISTS `craft_fieldlayouttabs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `layoutId` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sortOrder` smallint(6) unsigned DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `craft_fieldlayouttabs_sortOrder_idx` (`sortOrder`),
  KEY `craft_fieldlayouttabs_layoutId_fk` (`layoutId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `craft_fieldlayouttabs`
--

INSERT INTO `craft_fieldlayouttabs` (`id`, `layoutId`, `name`, `sortOrder`, `dateCreated`, `dateUpdated`, `uid`) VALUES
(1, 3, 'Content', 1, '2016-04-19 08:12:57', '2016-04-19 08:12:57', 'cd9349ce-13d7-4b8a-b26a-b1db8f8cfc02'),
(2, 5, 'Content', 1, '2016-04-19 08:12:57', '2016-04-19 08:12:57', '93335e84-79ae-4237-8593-b1b0fabf3bbf');

-- --------------------------------------------------------

--
-- Table structure for table `craft_fields`
--

DROP TABLE IF EXISTS `craft_fields`;
CREATE TABLE IF NOT EXISTS `craft_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupId` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `handle` varchar(58) COLLATE utf8_unicode_ci NOT NULL,
  `context` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'global',
  `instructions` text COLLATE utf8_unicode_ci,
  `translatable` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `type` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `settings` text COLLATE utf8_unicode_ci,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_fields_handle_context_unq_idx` (`handle`,`context`),
  KEY `craft_fields_context_idx` (`context`),
  KEY `craft_fields_groupId_fk` (`groupId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `craft_fields`
--

INSERT INTO `craft_fields` (`id`, `groupId`, `name`, `handle`, `context`, `instructions`, `translatable`, `type`, `settings`, `dateCreated`, `dateUpdated`, `uid`) VALUES
(1, 1, 'Body', 'body', 'global', NULL, 1, 'RichText', '{"configFile":"Standard.json","columnType":"text"}', '2016-04-19 08:12:57', '2016-04-19 08:12:57', '358e1dab-9abb-46ff-a0f2-7b6e1fcda03b'),
(2, 1, 'Tags', 'tags', 'global', NULL, 0, 'Tags', '{"source":"taggroup:1"}', '2016-04-19 08:12:57', '2016-04-19 08:12:57', '575b6dc0-79be-425d-a62d-9f7b945c8e81');

-- --------------------------------------------------------

--
-- Table structure for table `craft_globalsets`
--

DROP TABLE IF EXISTS `craft_globalsets`;
CREATE TABLE IF NOT EXISTS `craft_globalsets` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `handle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fieldLayoutId` int(10) DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_globalsets_name_unq_idx` (`name`),
  UNIQUE KEY `craft_globalsets_handle_unq_idx` (`handle`),
  KEY `craft_globalsets_fieldLayoutId_fk` (`fieldLayoutId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `craft_info`
--

DROP TABLE IF EXISTS `craft_info`;
CREATE TABLE IF NOT EXISTS `craft_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `build` int(11) unsigned NOT NULL,
  `schemaVersion` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `releaseDate` datetime NOT NULL,
  `edition` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `siteName` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `siteUrl` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `timezone` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `on` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `maintenance` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `track` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `craft_info`
--

INSERT INTO `craft_info` (`id`, `version`, `build`, `schemaVersion`, `releaseDate`, `edition`, `siteName`, `siteUrl`, `timezone`, `on`, `maintenance`, `track`, `dateCreated`, `dateUpdated`, `uid`) VALUES
(1, '2.6', 2780, '2.6.3', '2016-04-05 21:38:27', 0, 'Craft Seatmapevents', 'http://craft.seatmapevents.dev', 'UTC', 1, 0, 'stable', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '11ff024f-d961-4c39-adcf-7ccc54181264');

-- --------------------------------------------------------

--
-- Table structure for table `craft_locales`
--

DROP TABLE IF EXISTS `craft_locales`;
CREATE TABLE IF NOT EXISTS `craft_locales` (
  `locale` char(12) COLLATE utf8_unicode_ci NOT NULL,
  `sortOrder` smallint(6) unsigned DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`locale`),
  KEY `craft_locales_sortOrder_idx` (`sortOrder`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `craft_locales`
--

INSERT INTO `craft_locales` (`locale`, `sortOrder`, `dateCreated`, `dateUpdated`, `uid`) VALUES
('en_us', 1, '2016-04-19 08:12:55', '2016-04-19 08:12:55', 'a426cb9f-1171-44ec-8bbf-287f9319994e');

-- --------------------------------------------------------

--
-- Table structure for table `craft_matrixblocks`
--

DROP TABLE IF EXISTS `craft_matrixblocks`;
CREATE TABLE IF NOT EXISTS `craft_matrixblocks` (
  `id` int(11) NOT NULL,
  `ownerId` int(11) NOT NULL,
  `fieldId` int(11) NOT NULL,
  `typeId` int(11) DEFAULT NULL,
  `sortOrder` smallint(6) unsigned DEFAULT NULL,
  `ownerLocale` char(12) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `craft_matrixblocks_ownerId_idx` (`ownerId`),
  KEY `craft_matrixblocks_fieldId_idx` (`fieldId`),
  KEY `craft_matrixblocks_typeId_idx` (`typeId`),
  KEY `craft_matrixblocks_sortOrder_idx` (`sortOrder`),
  KEY `craft_matrixblocks_ownerLocale_fk` (`ownerLocale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `craft_matrixblocktypes`
--

DROP TABLE IF EXISTS `craft_matrixblocktypes`;
CREATE TABLE IF NOT EXISTS `craft_matrixblocktypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fieldId` int(11) NOT NULL,
  `fieldLayoutId` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `handle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sortOrder` smallint(6) unsigned DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_matrixblocktypes_name_fieldId_unq_idx` (`name`,`fieldId`),
  UNIQUE KEY `craft_matrixblocktypes_handle_fieldId_unq_idx` (`handle`,`fieldId`),
  KEY `craft_matrixblocktypes_fieldId_fk` (`fieldId`),
  KEY `craft_matrixblocktypes_fieldLayoutId_fk` (`fieldLayoutId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `craft_migrations`
--

DROP TABLE IF EXISTS `craft_migrations`;
CREATE TABLE IF NOT EXISTS `craft_migrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pluginId` int(11) DEFAULT NULL,
  `version` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `applyTime` datetime NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_migrations_version_unq_idx` (`version`),
  KEY `craft_migrations_pluginId_fk` (`pluginId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=32 ;

--
-- Dumping data for table `craft_migrations`
--

INSERT INTO `craft_migrations` (`id`, `pluginId`, `version`, `applyTime`, `dateCreated`, `dateUpdated`, `uid`) VALUES
(1, NULL, 'm000000_000000_base', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '310ab146-e42b-4f43-8987-61ca7a31c9ca'),
(2, NULL, 'm140730_000001_add_filename_and_format_to_transformindex', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '6cffab10-a5e9-4034-aba8-721496111ba8'),
(3, NULL, 'm140815_000001_add_format_to_transforms', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', 'e4ea57fb-e1b4-40a2-a399-b4ee9029421f'),
(4, NULL, 'm140822_000001_allow_more_than_128_items_per_field', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '48911bd1-56d3-4f84-9bcf-5aa60d1ab397'),
(5, NULL, 'm140829_000001_single_title_formats', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', 'ac7f9178-27bc-4ec0-99e8-656013335365'),
(6, NULL, 'm140831_000001_extended_cache_keys', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', 'da5ad66b-0334-458e-8cea-4379744e312e'),
(7, NULL, 'm140922_000001_delete_orphaned_matrix_blocks', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '494c694d-968a-425d-b7b6-f5680215ad42'),
(8, NULL, 'm141008_000001_elements_index_tune', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '88c4f0fe-3f85-4569-afd3-e87160e2714b'),
(9, NULL, 'm141009_000001_assets_source_handle', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', 'f803823e-205b-412e-980e-25f44ea54f0e'),
(10, NULL, 'm141024_000001_field_layout_tabs', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', 'eea21da9-1bef-473d-a465-ba3e375386c2'),
(11, NULL, 'm141030_000000_plugin_schema_versions', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '9017df89-3b53-4b8f-b814-55362ba6a909'),
(12, NULL, 'm141030_000001_drop_structure_move_permission', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '5a28467c-621e-45bc-a4c2-298eded3517d'),
(13, NULL, 'm141103_000001_tag_titles', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '44abd6e8-9638-4b37-a470-7a0c49c56772'),
(14, NULL, 'm141109_000001_user_status_shuffle', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', 'c8daf4c6-7f3c-41b5-b391-87e5abb5f2ce'),
(15, NULL, 'm141126_000001_user_week_start_day', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '985e950e-b75d-4f38-86ed-bf6bcddf614d'),
(16, NULL, 'm150210_000001_adjust_user_photo_size', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '6527672a-505a-406a-be1c-fccd1893ec8b'),
(17, NULL, 'm150724_000001_adjust_quality_settings', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', 'c3e7bff7-5019-4630-928b-2aa8a7c34ab0'),
(18, NULL, 'm150827_000000_element_index_settings', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '176235b9-9449-4720-847d-640596bca7fb'),
(19, NULL, 'm150918_000001_add_colspan_to_widgets', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '396bd74f-511c-45e2-81fb-74f93c08af9e'),
(20, NULL, 'm151007_000000_clear_asset_caches', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', 'ba517713-adfc-411a-8304-8023e111aa66'),
(21, NULL, 'm151109_000000_text_url_formats', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '152d5cfb-20a1-453d-8596-5b2ef4dca016'),
(22, NULL, 'm151110_000000_move_logo', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', 'ff05dde8-6666-4302-90e8-901db831c2d3'),
(23, NULL, 'm151117_000000_adjust_image_widthheight', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', 'e9f265e8-06f9-49f9-b272-4880d7fd53de'),
(24, NULL, 'm151127_000000_clear_license_key_status', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '0a0a6871-20b3-46a7-b7b9-efd89f95c82f'),
(25, NULL, 'm151127_000000_plugin_license_keys', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', 'a706a48a-cdd5-4c38-99c9-9ae656649b7e'),
(26, NULL, 'm151130_000000_update_pt_widget_feeds', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', 'cbad1444-d4be-4da9-b6d0-d44adb873ecc'),
(27, NULL, 'm160114_000000_asset_sources_public_url_default_true', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', 'dd9bd38a-80af-4e22-96bb-8c7b2864efe6'),
(28, NULL, 'm160223_000000_sortorder_to_smallint', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '24174668-5536-495e-9bf3-0c02694977c1'),
(29, NULL, 'm160229_000000_set_default_entry_statuses', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '60033ed0-525f-4e3e-9491-9d986b2074e5'),
(30, NULL, 'm160304_000000_client_permissions', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '07f5e1cb-e804-4bf5-ba68-3a80d3c8f8a8'),
(31, NULL, 'm160322_000000_asset_filesize', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '2016-04-19 08:12:55', '44c95838-32eb-48ec-a9e8-9c99d26784bf');

-- --------------------------------------------------------

--
-- Table structure for table `craft_plugins`
--

DROP TABLE IF EXISTS `craft_plugins`;
CREATE TABLE IF NOT EXISTS `craft_plugins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `version` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `schemaVersion` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `licenseKey` char(24) COLLATE utf8_unicode_ci DEFAULT NULL,
  `licenseKeyStatus` enum('valid','invalid','mismatched','unknown') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'unknown',
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `settings` text COLLATE utf8_unicode_ci,
  `installDate` datetime NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `craft_rackspaceaccess`
--

DROP TABLE IF EXISTS `craft_rackspaceaccess`;
CREATE TABLE IF NOT EXISTS `craft_rackspaceaccess` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `connectionKey` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `storageUrl` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cdnUrl` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_rackspaceaccess_connectionKey_unq_idx` (`connectionKey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `craft_relations`
--

DROP TABLE IF EXISTS `craft_relations`;
CREATE TABLE IF NOT EXISTS `craft_relations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fieldId` int(11) NOT NULL,
  `sourceId` int(11) NOT NULL,
  `sourceLocale` char(12) COLLATE utf8_unicode_ci DEFAULT NULL,
  `targetId` int(11) NOT NULL,
  `sortOrder` smallint(6) DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_relations_fieldId_sourceId_sourceLocale_targetId_unq_idx` (`fieldId`,`sourceId`,`sourceLocale`,`targetId`),
  KEY `craft_relations_sourceId_fk` (`sourceId`),
  KEY `craft_relations_sourceLocale_fk` (`sourceLocale`),
  KEY `craft_relations_targetId_fk` (`targetId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `craft_routes`
--

DROP TABLE IF EXISTS `craft_routes`;
CREATE TABLE IF NOT EXISTS `craft_routes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `locale` char(12) COLLATE utf8_unicode_ci DEFAULT NULL,
  `urlParts` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `urlPattern` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `template` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sortOrder` smallint(6) unsigned DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_routes_urlPattern_unq_idx` (`urlPattern`),
  KEY `craft_routes_locale_idx` (`locale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `craft_searchindex`
--

DROP TABLE IF EXISTS `craft_searchindex`;
CREATE TABLE IF NOT EXISTS `craft_searchindex` (
  `elementId` int(11) NOT NULL,
  `attribute` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `fieldId` int(11) NOT NULL,
  `locale` char(12) COLLATE utf8_unicode_ci NOT NULL,
  `keywords` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`elementId`,`attribute`,`fieldId`,`locale`),
  FULLTEXT KEY `craft_searchindex_keywords_idx` (`keywords`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `craft_searchindex`
--

INSERT INTO `craft_searchindex` (`elementId`, `attribute`, `fieldId`, `locale`, `keywords`) VALUES
(1, 'username', 0, 'en_us', ' admin '),
(1, 'firstname', 0, 'en_us', ''),
(1, 'lastname', 0, 'en_us', ''),
(1, 'fullname', 0, 'en_us', ''),
(1, 'email', 0, 'en_us', ' admin craft seatmapevents dev '),
(1, 'slug', 0, 'en_us', ''),
(2, 'slug', 0, 'en_us', ' homepage '),
(2, 'title', 0, 'en_us', ' welcome to craft seatmapevents dev '),
(2, 'field', 1, 'en_us', ' it s true this site doesn t have a whole lot of content yet but don t worry our web developers have just installed the cms and they re setting things up for the content editors this very moment soon craft seatmapevents dev will be an oasis of fresh perspectives sharp analyses and astute opinions that will keep you coming back again and again '),
(3, 'field', 1, 'en_us', ' craft is the cms that s powering craft seatmapevents dev it s beautiful powerful flexible and easy to use and it s made by pixel tonic we can t wait to dive in and see what it s capable of this is even more captivating content which you couldn t see on the news index page because it was entered after a page break and the news index template only likes to show the content on the first page craft a nice alternative to word if you re making a website '),
(3, 'field', 2, 'en_us', ''),
(3, 'slug', 0, 'en_us', ''),
(3, 'title', 0, 'en_us', ' we just installed craft ');

-- --------------------------------------------------------

--
-- Table structure for table `craft_sections`
--

DROP TABLE IF EXISTS `craft_sections`;
CREATE TABLE IF NOT EXISTS `craft_sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `structureId` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `handle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` enum('single','channel','structure') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'channel',
  `hasUrls` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `template` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `enableVersioning` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_sections_name_unq_idx` (`name`),
  UNIQUE KEY `craft_sections_handle_unq_idx` (`handle`),
  KEY `craft_sections_structureId_fk` (`structureId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `craft_sections`
--

INSERT INTO `craft_sections` (`id`, `structureId`, `name`, `handle`, `type`, `hasUrls`, `template`, `enableVersioning`, `dateCreated`, `dateUpdated`, `uid`) VALUES
(1, NULL, 'Homepage', 'homepage', 'single', 1, 'index', 1, '2016-04-19 08:12:57', '2016-04-19 08:12:57', '47514740-8099-4a06-85a3-d9b4f0927463'),
(2, NULL, 'News', 'news', 'channel', 1, 'news/_entry', 1, '2016-04-19 08:12:57', '2016-04-19 08:12:57', '1f5545df-cf6d-4b8d-9429-c3d4a70e61f4');

-- --------------------------------------------------------

--
-- Table structure for table `craft_sections_i18n`
--

DROP TABLE IF EXISTS `craft_sections_i18n`;
CREATE TABLE IF NOT EXISTS `craft_sections_i18n` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sectionId` int(11) NOT NULL,
  `locale` char(12) COLLATE utf8_unicode_ci NOT NULL,
  `enabledByDefault` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `urlFormat` text COLLATE utf8_unicode_ci,
  `nestedUrlFormat` text COLLATE utf8_unicode_ci,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_sections_i18n_sectionId_locale_unq_idx` (`sectionId`,`locale`),
  KEY `craft_sections_i18n_locale_fk` (`locale`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `craft_sections_i18n`
--

INSERT INTO `craft_sections_i18n` (`id`, `sectionId`, `locale`, `enabledByDefault`, `urlFormat`, `nestedUrlFormat`, `dateCreated`, `dateUpdated`, `uid`) VALUES
(1, 1, 'en_us', 1, '__home__', NULL, '2016-04-19 08:12:57', '2016-04-19 08:12:57', '6fb51964-fac2-4deb-a739-3c60599bd340'),
(2, 2, 'en_us', 1, 'news/{postDate.year}/{slug}', NULL, '2016-04-19 08:12:57', '2016-04-19 08:12:57', '066043bd-9e66-43ef-b507-b9837ae9f6b9');

-- --------------------------------------------------------

--
-- Table structure for table `craft_sessions`
--

DROP TABLE IF EXISTS `craft_sessions`;
CREATE TABLE IF NOT EXISTS `craft_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `token` char(100) COLLATE utf8_unicode_ci NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `craft_sessions_uid_idx` (`uid`),
  KEY `craft_sessions_token_idx` (`token`),
  KEY `craft_sessions_dateUpdated_idx` (`dateUpdated`),
  KEY `craft_sessions_userId_fk` (`userId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `craft_sessions`
--

INSERT INTO `craft_sessions` (`id`, `userId`, `token`, `dateCreated`, `dateUpdated`, `uid`) VALUES
(1, 1, 'bc82442bfb7465855d6da3d3c97847db47398789czozMjoiZld6UTU4bGQzYVlPMWtJU1pGWlljVXBiOWRmdHMzX0EiOw==', '2016-04-19 08:12:57', '2016-04-19 08:12:57', 'c695613a-81f1-44d7-b845-ed2ae47ec43f');

-- --------------------------------------------------------

--
-- Table structure for table `craft_shunnedmessages`
--

DROP TABLE IF EXISTS `craft_shunnedmessages`;
CREATE TABLE IF NOT EXISTS `craft_shunnedmessages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `message` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `expiryDate` datetime DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_shunnedmessages_userId_message_unq_idx` (`userId`,`message`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `craft_structureelements`
--

DROP TABLE IF EXISTS `craft_structureelements`;
CREATE TABLE IF NOT EXISTS `craft_structureelements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `structureId` int(11) NOT NULL,
  `elementId` int(11) DEFAULT NULL,
  `root` int(11) unsigned DEFAULT NULL,
  `lft` int(11) unsigned NOT NULL,
  `rgt` int(11) unsigned NOT NULL,
  `level` smallint(6) unsigned NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_structureelements_structureId_elementId_unq_idx` (`structureId`,`elementId`),
  KEY `craft_structureelements_root_idx` (`root`),
  KEY `craft_structureelements_lft_idx` (`lft`),
  KEY `craft_structureelements_rgt_idx` (`rgt`),
  KEY `craft_structureelements_level_idx` (`level`),
  KEY `craft_structureelements_elementId_fk` (`elementId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `craft_structures`
--

DROP TABLE IF EXISTS `craft_structures`;
CREATE TABLE IF NOT EXISTS `craft_structures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `maxLevels` smallint(6) unsigned DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `craft_systemsettings`
--

DROP TABLE IF EXISTS `craft_systemsettings`;
CREATE TABLE IF NOT EXISTS `craft_systemsettings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `settings` text COLLATE utf8_unicode_ci,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_systemsettings_category_unq_idx` (`category`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `craft_systemsettings`
--

INSERT INTO `craft_systemsettings` (`id`, `category`, `settings`, `dateCreated`, `dateUpdated`, `uid`) VALUES
(1, 'email', '{"protocol":"php","emailAddress":"admin@craft.seatmapevents.dev","senderName":"Craft Seatmapevents"}', '2016-04-19 08:12:57', '2016-04-19 08:12:57', 'e2bdba8e-6f22-4b54-9e31-3570c8b7ac20');

-- --------------------------------------------------------

--
-- Table structure for table `craft_taggroups`
--

DROP TABLE IF EXISTS `craft_taggroups`;
CREATE TABLE IF NOT EXISTS `craft_taggroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `handle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fieldLayoutId` int(10) DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_taggroups_name_unq_idx` (`name`),
  UNIQUE KEY `craft_taggroups_handle_unq_idx` (`handle`),
  KEY `craft_taggroups_fieldLayoutId_fk` (`fieldLayoutId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `craft_taggroups`
--

INSERT INTO `craft_taggroups` (`id`, `name`, `handle`, `fieldLayoutId`, `dateCreated`, `dateUpdated`, `uid`) VALUES
(1, 'Default', 'default', 1, '2016-04-19 08:12:57', '2016-04-19 08:12:57', '71c4fc97-0917-4bc8-b634-ad64033cb205');

-- --------------------------------------------------------

--
-- Table structure for table `craft_tags`
--

DROP TABLE IF EXISTS `craft_tags`;
CREATE TABLE IF NOT EXISTS `craft_tags` (
  `id` int(11) NOT NULL,
  `groupId` int(11) NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `craft_tags_groupId_fk` (`groupId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `craft_tasks`
--

DROP TABLE IF EXISTS `craft_tasks`;
CREATE TABLE IF NOT EXISTS `craft_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `root` int(11) unsigned DEFAULT NULL,
  `lft` int(11) unsigned NOT NULL,
  `rgt` int(11) unsigned NOT NULL,
  `level` smallint(6) unsigned NOT NULL,
  `currentStep` int(11) unsigned DEFAULT NULL,
  `totalSteps` int(11) unsigned DEFAULT NULL,
  `status` enum('pending','error','running') COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `settings` text COLLATE utf8_unicode_ci,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `craft_tasks_root_idx` (`root`),
  KEY `craft_tasks_lft_idx` (`lft`),
  KEY `craft_tasks_rgt_idx` (`rgt`),
  KEY `craft_tasks_level_idx` (`level`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `craft_tasks`
--

INSERT INTO `craft_tasks` (`id`, `root`, `lft`, `rgt`, `level`, `currentStep`, `totalSteps`, `status`, `type`, `description`, `settings`, `dateCreated`, `dateUpdated`, `uid`) VALUES
(1, 1, 1, 2, 0, NULL, NULL, 'pending', 'DeleteStaleTemplateCaches', NULL, '{"elementId":["1","2","3"]}', '2016-04-19 08:12:56', '2016-04-19 08:12:57', '28a71f50-f102-4ecc-80cb-4ba3e9c4b823');

-- --------------------------------------------------------

--
-- Table structure for table `craft_templatecachecriteria`
--

DROP TABLE IF EXISTS `craft_templatecachecriteria`;
CREATE TABLE IF NOT EXISTS `craft_templatecachecriteria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cacheId` int(11) NOT NULL,
  `type` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `criteria` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `craft_templatecachecriteria_cacheId_fk` (`cacheId`),
  KEY `craft_templatecachecriteria_type_idx` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `craft_templatecacheelements`
--

DROP TABLE IF EXISTS `craft_templatecacheelements`;
CREATE TABLE IF NOT EXISTS `craft_templatecacheelements` (
  `cacheId` int(11) NOT NULL,
  `elementId` int(11) NOT NULL,
  KEY `craft_templatecacheelements_cacheId_fk` (`cacheId`),
  KEY `craft_templatecacheelements_elementId_fk` (`elementId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `craft_templatecaches`
--

DROP TABLE IF EXISTS `craft_templatecaches`;
CREATE TABLE IF NOT EXISTS `craft_templatecaches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cacheKey` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `locale` char(12) COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `expiryDate` datetime NOT NULL,
  `body` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `craft_templatecaches_expiryDate_cacheKey_locale_path_idx` (`expiryDate`,`cacheKey`,`locale`,`path`),
  KEY `craft_templatecaches_locale_fk` (`locale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `craft_tokens`
--

DROP TABLE IF EXISTS `craft_tokens`;
CREATE TABLE IF NOT EXISTS `craft_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` char(32) COLLATE utf8_unicode_ci NOT NULL,
  `route` text COLLATE utf8_unicode_ci,
  `usageLimit` tinyint(3) unsigned DEFAULT NULL,
  `usageCount` tinyint(3) unsigned DEFAULT NULL,
  `expiryDate` datetime NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_tokens_token_unq_idx` (`token`),
  KEY `craft_tokens_expiryDate_idx` (`expiryDate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `craft_usergroups`
--

DROP TABLE IF EXISTS `craft_usergroups`;
CREATE TABLE IF NOT EXISTS `craft_usergroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `handle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `craft_usergroups_users`
--

DROP TABLE IF EXISTS `craft_usergroups_users`;
CREATE TABLE IF NOT EXISTS `craft_usergroups_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_usergroups_users_groupId_userId_unq_idx` (`groupId`,`userId`),
  KEY `craft_usergroups_users_userId_fk` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `craft_userpermissions`
--

DROP TABLE IF EXISTS `craft_userpermissions`;
CREATE TABLE IF NOT EXISTS `craft_userpermissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_userpermissions_name_unq_idx` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `craft_userpermissions_usergroups`
--

DROP TABLE IF EXISTS `craft_userpermissions_usergroups`;
CREATE TABLE IF NOT EXISTS `craft_userpermissions_usergroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `permissionId` int(11) NOT NULL,
  `groupId` int(11) NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_userpermissions_usergroups_permissionId_groupId_unq_idx` (`permissionId`,`groupId`),
  KEY `craft_userpermissions_usergroups_groupId_fk` (`groupId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `craft_userpermissions_users`
--

DROP TABLE IF EXISTS `craft_userpermissions_users`;
CREATE TABLE IF NOT EXISTS `craft_userpermissions_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `permissionId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_userpermissions_users_permissionId_userId_unq_idx` (`permissionId`,`userId`),
  KEY `craft_userpermissions_users_userId_fk` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `craft_users`
--

DROP TABLE IF EXISTS `craft_users`;
CREATE TABLE IF NOT EXISTS `craft_users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `photo` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `firstName` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastName` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` char(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `preferredLocale` char(12) COLLATE utf8_unicode_ci DEFAULT NULL,
  `weekStartDay` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `admin` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `client` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `locked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `suspended` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pending` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `archived` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `lastLoginDate` datetime DEFAULT NULL,
  `lastLoginAttemptIPAddress` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `invalidLoginWindowStart` datetime DEFAULT NULL,
  `invalidLoginCount` tinyint(4) unsigned DEFAULT NULL,
  `lastInvalidLoginDate` datetime DEFAULT NULL,
  `lockoutDate` datetime DEFAULT NULL,
  `verificationCode` char(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `verificationCodeIssuedDate` datetime DEFAULT NULL,
  `unverifiedEmail` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `passwordResetRequired` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `lastPasswordChangeDate` datetime DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `craft_users_username_unq_idx` (`username`),
  UNIQUE KEY `craft_users_email_unq_idx` (`email`),
  KEY `craft_users_verificationCode_idx` (`verificationCode`),
  KEY `craft_users_uid_idx` (`uid`),
  KEY `craft_users_preferredLocale_fk` (`preferredLocale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `craft_users`
--

INSERT INTO `craft_users` (`id`, `username`, `photo`, `firstName`, `lastName`, `email`, `password`, `preferredLocale`, `weekStartDay`, `admin`, `client`, `locked`, `suspended`, `pending`, `archived`, `lastLoginDate`, `lastLoginAttemptIPAddress`, `invalidLoginWindowStart`, `invalidLoginCount`, `lastInvalidLoginDate`, `lockoutDate`, `verificationCode`, `verificationCodeIssuedDate`, `unverifiedEmail`, `passwordResetRequired`, `lastPasswordChangeDate`, `dateCreated`, `dateUpdated`, `uid`) VALUES
(1, 'admin', NULL, NULL, NULL, 'admin@craft.seatmapevents.dev', '$2y$13$xe9Ey0j5K60GLJwk8tGec.JacQcxOkGfp5Xwyslm5nYGrCHJi0fNy', NULL, 0, 1, 0, 0, 0, 0, 0, '2016-04-19 08:12:57', '192.168.50.1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2016-04-19 08:12:56', '2016-04-19 08:12:56', '2016-04-19 08:12:57', '1b087082-31c5-43ce-8bc8-68ae8fa8525e');

-- --------------------------------------------------------

--
-- Table structure for table `craft_widgets`
--

DROP TABLE IF EXISTS `craft_widgets`;
CREATE TABLE IF NOT EXISTS `craft_widgets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `type` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `sortOrder` smallint(6) unsigned DEFAULT NULL,
  `colspan` tinyint(4) unsigned DEFAULT NULL,
  `settings` text COLLATE utf8_unicode_ci,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `craft_widgets_userId_fk` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `craft_assetfiles`
--
ALTER TABLE `craft_assetfiles`
  ADD CONSTRAINT `craft_assetfiles_folderId_fk` FOREIGN KEY (`folderId`) REFERENCES `craft_assetfolders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `craft_assetfiles_id_fk` FOREIGN KEY (`id`) REFERENCES `craft_elements` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `craft_assetfiles_sourceId_fk` FOREIGN KEY (`sourceId`) REFERENCES `craft_assetsources` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `craft_assetfolders`
--
ALTER TABLE `craft_assetfolders`
  ADD CONSTRAINT `craft_assetfolders_sourceId_fk` FOREIGN KEY (`sourceId`) REFERENCES `craft_assetsources` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `craft_assetfolders_parentId_fk` FOREIGN KEY (`parentId`) REFERENCES `craft_assetfolders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `craft_assetindexdata`
--
ALTER TABLE `craft_assetindexdata`
  ADD CONSTRAINT `craft_assetindexdata_sourceId_fk` FOREIGN KEY (`sourceId`) REFERENCES `craft_assetsources` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `craft_assetsources`
--
ALTER TABLE `craft_assetsources`
  ADD CONSTRAINT `craft_assetsources_fieldLayoutId_fk` FOREIGN KEY (`fieldLayoutId`) REFERENCES `craft_fieldlayouts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `craft_categories`
--
ALTER TABLE `craft_categories`
  ADD CONSTRAINT `craft_categories_groupId_fk` FOREIGN KEY (`groupId`) REFERENCES `craft_categorygroups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `craft_categories_id_fk` FOREIGN KEY (`id`) REFERENCES `craft_elements` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `craft_categorygroups`
--
ALTER TABLE `craft_categorygroups`
  ADD CONSTRAINT `craft_categorygroups_fieldLayoutId_fk` FOREIGN KEY (`fieldLayoutId`) REFERENCES `craft_fieldlayouts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `craft_categorygroups_structureId_fk` FOREIGN KEY (`structureId`) REFERENCES `craft_structures` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `craft_categorygroups_i18n`
--
ALTER TABLE `craft_categorygroups_i18n`
  ADD CONSTRAINT `craft_categorygroups_i18n_locale_fk` FOREIGN KEY (`locale`) REFERENCES `craft_locales` (`locale`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `craft_categorygroups_i18n_groupId_fk` FOREIGN KEY (`groupId`) REFERENCES `craft_categorygroups` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `craft_content`
--
ALTER TABLE `craft_content`
  ADD CONSTRAINT `craft_content_elementId_fk` FOREIGN KEY (`elementId`) REFERENCES `craft_elements` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `craft_content_locale_fk` FOREIGN KEY (`locale`) REFERENCES `craft_locales` (`locale`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `craft_elements_i18n`
--
ALTER TABLE `craft_elements_i18n`
  ADD CONSTRAINT `craft_elements_i18n_locale_fk` FOREIGN KEY (`locale`) REFERENCES `craft_locales` (`locale`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `craft_elements_i18n_elementId_fk` FOREIGN KEY (`elementId`) REFERENCES `craft_elements` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `craft_emailmessages`
--
ALTER TABLE `craft_emailmessages`
  ADD CONSTRAINT `craft_emailmessages_locale_fk` FOREIGN KEY (`locale`) REFERENCES `craft_locales` (`locale`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `craft_entries`
--
ALTER TABLE `craft_entries`
  ADD CONSTRAINT `craft_entries_authorId_fk` FOREIGN KEY (`authorId`) REFERENCES `craft_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `craft_entries_id_fk` FOREIGN KEY (`id`) REFERENCES `craft_elements` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `craft_entries_sectionId_fk` FOREIGN KEY (`sectionId`) REFERENCES `craft_sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `craft_entries_typeId_fk` FOREIGN KEY (`typeId`) REFERENCES `craft_entrytypes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `craft_entrydrafts`
--
ALTER TABLE `craft_entrydrafts`
  ADD CONSTRAINT `craft_entrydrafts_locale_fk` FOREIGN KEY (`locale`) REFERENCES `craft_locales` (`locale`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `craft_entrydrafts_creatorId_fk` FOREIGN KEY (`creatorId`) REFERENCES `craft_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `craft_entrydrafts_entryId_fk` FOREIGN KEY (`entryId`) REFERENCES `craft_entries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `craft_entrydrafts_sectionId_fk` FOREIGN KEY (`sectionId`) REFERENCES `craft_sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `craft_entrytypes`
--
ALTER TABLE `craft_entrytypes`
  ADD CONSTRAINT `craft_entrytypes_fieldLayoutId_fk` FOREIGN KEY (`fieldLayoutId`) REFERENCES `craft_fieldlayouts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `craft_entrytypes_sectionId_fk` FOREIGN KEY (`sectionId`) REFERENCES `craft_sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `craft_entryversions`
--
ALTER TABLE `craft_entryversions`
  ADD CONSTRAINT `craft_entryversions_locale_fk` FOREIGN KEY (`locale`) REFERENCES `craft_locales` (`locale`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `craft_entryversions_creatorId_fk` FOREIGN KEY (`creatorId`) REFERENCES `craft_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `craft_entryversions_entryId_fk` FOREIGN KEY (`entryId`) REFERENCES `craft_entries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `craft_entryversions_sectionId_fk` FOREIGN KEY (`sectionId`) REFERENCES `craft_sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `craft_fieldlayoutfields`
--
ALTER TABLE `craft_fieldlayoutfields`
  ADD CONSTRAINT `craft_fieldlayoutfields_fieldId_fk` FOREIGN KEY (`fieldId`) REFERENCES `craft_fields` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `craft_fieldlayoutfields_layoutId_fk` FOREIGN KEY (`layoutId`) REFERENCES `craft_fieldlayouts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `craft_fieldlayoutfields_tabId_fk` FOREIGN KEY (`tabId`) REFERENCES `craft_fieldlayouttabs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `craft_fieldlayouttabs`
--
ALTER TABLE `craft_fieldlayouttabs`
  ADD CONSTRAINT `craft_fieldlayouttabs_layoutId_fk` FOREIGN KEY (`layoutId`) REFERENCES `craft_fieldlayouts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `craft_fields`
--
ALTER TABLE `craft_fields`
  ADD CONSTRAINT `craft_fields_groupId_fk` FOREIGN KEY (`groupId`) REFERENCES `craft_fieldgroups` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `craft_globalsets`
--
ALTER TABLE `craft_globalsets`
  ADD CONSTRAINT `craft_globalsets_fieldLayoutId_fk` FOREIGN KEY (`fieldLayoutId`) REFERENCES `craft_fieldlayouts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `craft_globalsets_id_fk` FOREIGN KEY (`id`) REFERENCES `craft_elements` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `craft_matrixblocks`
--
ALTER TABLE `craft_matrixblocks`
  ADD CONSTRAINT `craft_matrixblocks_typeId_fk` FOREIGN KEY (`typeId`) REFERENCES `craft_matrixblocktypes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `craft_matrixblocks_fieldId_fk` FOREIGN KEY (`fieldId`) REFERENCES `craft_fields` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `craft_matrixblocks_id_fk` FOREIGN KEY (`id`) REFERENCES `craft_elements` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `craft_matrixblocks_ownerId_fk` FOREIGN KEY (`ownerId`) REFERENCES `craft_elements` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `craft_matrixblocks_ownerLocale_fk` FOREIGN KEY (`ownerLocale`) REFERENCES `craft_locales` (`locale`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `craft_matrixblocktypes`
--
ALTER TABLE `craft_matrixblocktypes`
  ADD CONSTRAINT `craft_matrixblocktypes_fieldLayoutId_fk` FOREIGN KEY (`fieldLayoutId`) REFERENCES `craft_fieldlayouts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `craft_matrixblocktypes_fieldId_fk` FOREIGN KEY (`fieldId`) REFERENCES `craft_fields` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `craft_migrations`
--
ALTER TABLE `craft_migrations`
  ADD CONSTRAINT `craft_migrations_pluginId_fk` FOREIGN KEY (`pluginId`) REFERENCES `craft_plugins` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `craft_relations`
--
ALTER TABLE `craft_relations`
  ADD CONSTRAINT `craft_relations_targetId_fk` FOREIGN KEY (`targetId`) REFERENCES `craft_elements` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `craft_relations_fieldId_fk` FOREIGN KEY (`fieldId`) REFERENCES `craft_fields` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `craft_relations_sourceId_fk` FOREIGN KEY (`sourceId`) REFERENCES `craft_elements` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `craft_relations_sourceLocale_fk` FOREIGN KEY (`sourceLocale`) REFERENCES `craft_locales` (`locale`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `craft_routes`
--
ALTER TABLE `craft_routes`
  ADD CONSTRAINT `craft_routes_locale_fk` FOREIGN KEY (`locale`) REFERENCES `craft_locales` (`locale`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `craft_sections`
--
ALTER TABLE `craft_sections`
  ADD CONSTRAINT `craft_sections_structureId_fk` FOREIGN KEY (`structureId`) REFERENCES `craft_structures` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `craft_sections_i18n`
--
ALTER TABLE `craft_sections_i18n`
  ADD CONSTRAINT `craft_sections_i18n_locale_fk` FOREIGN KEY (`locale`) REFERENCES `craft_locales` (`locale`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `craft_sections_i18n_sectionId_fk` FOREIGN KEY (`sectionId`) REFERENCES `craft_sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `craft_sessions`
--
ALTER TABLE `craft_sessions`
  ADD CONSTRAINT `craft_sessions_userId_fk` FOREIGN KEY (`userId`) REFERENCES `craft_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `craft_shunnedmessages`
--
ALTER TABLE `craft_shunnedmessages`
  ADD CONSTRAINT `craft_shunnedmessages_userId_fk` FOREIGN KEY (`userId`) REFERENCES `craft_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `craft_structureelements`
--
ALTER TABLE `craft_structureelements`
  ADD CONSTRAINT `craft_structureelements_elementId_fk` FOREIGN KEY (`elementId`) REFERENCES `craft_elements` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `craft_structureelements_structureId_fk` FOREIGN KEY (`structureId`) REFERENCES `craft_structures` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `craft_taggroups`
--
ALTER TABLE `craft_taggroups`
  ADD CONSTRAINT `craft_taggroups_fieldLayoutId_fk` FOREIGN KEY (`fieldLayoutId`) REFERENCES `craft_fieldlayouts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `craft_tags`
--
ALTER TABLE `craft_tags`
  ADD CONSTRAINT `craft_tags_groupId_fk` FOREIGN KEY (`groupId`) REFERENCES `craft_taggroups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `craft_tags_id_fk` FOREIGN KEY (`id`) REFERENCES `craft_elements` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `craft_templatecachecriteria`
--
ALTER TABLE `craft_templatecachecriteria`
  ADD CONSTRAINT `craft_templatecachecriteria_cacheId_fk` FOREIGN KEY (`cacheId`) REFERENCES `craft_templatecaches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `craft_templatecacheelements`
--
ALTER TABLE `craft_templatecacheelements`
  ADD CONSTRAINT `craft_templatecacheelements_elementId_fk` FOREIGN KEY (`elementId`) REFERENCES `craft_elements` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `craft_templatecacheelements_cacheId_fk` FOREIGN KEY (`cacheId`) REFERENCES `craft_templatecaches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `craft_templatecaches`
--
ALTER TABLE `craft_templatecaches`
  ADD CONSTRAINT `craft_templatecaches_locale_fk` FOREIGN KEY (`locale`) REFERENCES `craft_locales` (`locale`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `craft_usergroups_users`
--
ALTER TABLE `craft_usergroups_users`
  ADD CONSTRAINT `craft_usergroups_users_userId_fk` FOREIGN KEY (`userId`) REFERENCES `craft_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `craft_usergroups_users_groupId_fk` FOREIGN KEY (`groupId`) REFERENCES `craft_usergroups` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `craft_userpermissions_usergroups`
--
ALTER TABLE `craft_userpermissions_usergroups`
  ADD CONSTRAINT `craft_userpermissions_usergroups_groupId_fk` FOREIGN KEY (`groupId`) REFERENCES `craft_usergroups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `craft_userpermissions_usergroups_permissionId_fk` FOREIGN KEY (`permissionId`) REFERENCES `craft_userpermissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `craft_userpermissions_users`
--
ALTER TABLE `craft_userpermissions_users`
  ADD CONSTRAINT `craft_userpermissions_users_userId_fk` FOREIGN KEY (`userId`) REFERENCES `craft_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `craft_userpermissions_users_permissionId_fk` FOREIGN KEY (`permissionId`) REFERENCES `craft_userpermissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `craft_users`
--
ALTER TABLE `craft_users`
  ADD CONSTRAINT `craft_users_preferredLocale_fk` FOREIGN KEY (`preferredLocale`) REFERENCES `craft_locales` (`locale`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `craft_users_id_fk` FOREIGN KEY (`id`) REFERENCES `craft_elements` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `craft_widgets`
--
ALTER TABLE `craft_widgets`
  ADD CONSTRAINT `craft_widgets_userId_fk` FOREIGN KEY (`userId`) REFERENCES `craft_users` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

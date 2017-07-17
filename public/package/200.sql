--
-- 创建文章内容表
--
DROP TABLE IF EXISTS `mip_articles_approval`;
CREATE TABLE `mip_articles_approval` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(24) DEFAULT NULL,
  `cid` int(11) unsigned DEFAULT '0',
  `uid` int(11) unsigned DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `views` int(11) unsigned DEFAULT '0',
  `create_time` int(11) unsigned zerofill DEFAULT NULL,
  `edit_time` int(11) unsigned zerofill DEFAULT NULL,
  `is_recommend` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `comments` int(11) unsigned NOT NULL DEFAULT '0',
  `version` int(10) unsigned NOT NULL DEFAULT '0',
  `publish_time` int(11) unsigned zerofill DEFAULT NULL,
  `collect` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `unid` (`uuid`),
  KEY `cid` (`cid`),
  KEY `uid` (`uid`),
  KEY `publish_time` (`publish_time`),
  KEY `is_recommend` (`is_recommend`),
  KEY `views` (`views`) USING BTREE,
  KEY `create_time` (`create_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
 
 
 DROP TABLE IF EXISTS `mip_articles_content`;
CREATE TABLE `mip_articles_content` (
  `id` char(24) NOT NULL,
  `content` longtext,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
 DROP TABLE IF EXISTS `mip_articles_draft`;
CREATE TABLE `mip_articles_draft` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(24) DEFAULT NULL,
  `cid` int(11) unsigned DEFAULT '0',
  `uid` int(11) unsigned DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `content` longtext,
  `views` int(11) unsigned DEFAULT '0',
  `create_time` int(11) unsigned zerofill DEFAULT NULL,
  `edit_time` int(11) unsigned zerofill DEFAULT NULL,
  `is_recommend` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `comments` int(11) unsigned NOT NULL DEFAULT '0',
  `version` int(10) unsigned NOT NULL DEFAULT '0',
  `publish_time` int(11) unsigned zerofill DEFAULT NULL,
  `collect` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `unid` (`uuid`),
  KEY `cid` (`cid`),
  KEY `uid` (`uid`),
  KEY `publish_time` (`publish_time`),
  KEY `is_recommend` (`is_recommend`),
  KEY `views` (`views`) USING BTREE,
  KEY `create_time` (`create_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for mip_articles_setting
-- ----------------------------
DROP TABLE IF EXISTS `mip_articles_setting`;
CREATE TABLE `mip_articles_setting` (
  `key` varchar(255) CHARACTER SET utf8 NOT NULL,
  `val` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`key`),
  KEY `key` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


ALTER TABLE `mip_articles` ADD `content_id` char( 24 ) DEFAULT NULL;
-- ----------------------------
-- Records of mip_articles_setting
-- ----------------------------
INSERT INTO `mip_articles_setting` VALUES ('articlePublishName', '发布');
INSERT INTO `mip_articles_setting` VALUES ('articlePublishStatus', '1');
INSERT INTO `mip_articles_setting` VALUES ('articlePublishUserNumDay', '10');
INSERT INTO `mip_articles_setting` VALUES ('articlePublishUserTime', '10');
INSERT INTO `mip_articles_setting` VALUES ('articlePublishUserIntegral', '1');
INSERT INTO `mip_articles_setting` VALUES ('commentsStatus', '1');
INSERT INTO `mip_articles_setting` VALUES ('commentsShowNum', '20');
 
  
INSERT INTO `mip_settings` (`key`, `val`) VALUES ('https', 'http');
INSERT INTO `mip_settings` (`key`, `val`) VALUES ('mipApiAddress', '');
INSERT INTO `mip_settings` (`key`, `val`) VALUES ('articlePages', '');

 
--
-- 版本更新 每个版本都有
--

--
-- 更新当前版本 
--
UPDATE `mip_settings` SET `val` = '200' WHERE `key` = 'localCurrentVersionNum';

--
-- 更新当前版本副本
--
UPDATE `mip_settings` SET `val` = 'v2.0.0' WHERE `key` = 'localCurrentVersion';

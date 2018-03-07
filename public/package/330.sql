

ALTER TABLE `mip_articles_category` ADD `template` varchar(255) DEFAULT NULL;

ALTER TABLE `mip_product_category` ADD `template` varchar(255) DEFAULT NULL;

DROP TABLE IF EXISTS `mip_tags_category`;
CREATE TABLE `mip_tags_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `pid` tinyint(5) DEFAULT NULL,
  `sort` tinyint(5) DEFAULT '99',
  `keywords` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `url_name` varchar(255) DEFAULT NULL,
  `count_num` int(11) DEFAULT '0',
  `seo_title` varchar(255) DEFAULT NULL,
  `template` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `sort` (`sort`),
  KEY `pid` (`pid`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


ALTER TABLE `mip_tags` ADD `cid` int(11) DEFAULT NULL;


DROP TABLE IF EXISTS `mip_key`;
CREATE TABLE `mip_key` (
  `key` varchar(255) NOT NULL,
  `val` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

BEGIN;
INSERT INTO `mip_key` VALUES ('baiduXZClientId', ''), ('baiduXZClientSecret', ''), ('baiduXZToken', ''),('baiduXZRedirectUri', '');
COMMIT;


DROP TABLE IF EXISTS `mip_xiongzhang_auto_reply`;
CREATE TABLE `mip_xiongzhang_auto_reply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_content` varchar(255) DEFAULT NULL,
  `reply_content` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `add_time` int(11) DEFAULT NULL,
  `end_time` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `mip_addons`;
CREATE TABLE `mip_addons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `version` varchar(255) DEFAULT NULL,
  `add_time` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `config` text,
  `admin_url` varchar(255) DEFAULT NULL,
  `side_status` int(255) DEFAULT NULL,
  `header_status` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `add_time` (`add_time`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `mip_header_menu`;
CREATE TABLE `mip_header_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `admin_url` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `id` (`id`),
  KEY `sort` (`sort`),
  KEY `item_id` (`item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `mip_addons_menu`;
CREATE TABLE `mip_addons_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `admin_url` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `id` (`id`),
  KEY `sort` (`sort`),
  KEY `item_id` (`item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `mip_global_action`;
CREATE TABLE `mip_global_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `id` (`id`),
  KEY `sort` (`sort`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- 更新当前版本
--
UPDATE `mip_settings` SET `val` = '330' WHERE `key` = 'localCurrentVersionNum';
 
--
-- 更新当前版本副本
--
UPDATE `mip_settings` SET `val` = 'v3.3.0' WHERE `key` = 'localCurrentVersion';
SET FOREIGN_KEY_CHECKS = 1;

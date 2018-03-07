
ALTER TABLE `mip_articles` ADD `keywords` varchar(255) DEFAULT NULL;
ALTER TABLE `mip_articles` ADD `link_tags` varchar(255) DEFAULT NULL;
ALTER TABLE `mip_articles` ADD `description` varchar(255) DEFAULT NULL;
ALTER TABLE `mip_articles` ADD `site_id` char(24) DEFAULT NULL;
ALTER TABLE `mip_articles` ADD `mip_push_num` int(11) DEFAULT NULL;
ALTER TABLE `mip_articles` ADD `amp_push_num` int(11) DEFAULT NULL;
ALTER TABLE `mip_articles` ADD `xzh_push_num` int(11) DEFAULT NULL;
ALTER TABLE `mip_articles` ADD `link_push_num` int(11) DEFAULT NULL;
ALTER TABLE `mip_articles` ADD `yc_push_num` int(11) DEFAULT NULL;
ALTER TABLE `mip_articles` ADD `baidu_spider_num` int(11) DEFAULT NULL;
ALTER TABLE `mip_articles` ADD `google_spider_num` int(11) DEFAULT NULL;
ALTER TABLE `mip_articles` ADD `so_spider_num` int(11) DEFAULT NULL;
ALTER TABLE `mip_articles` ADD `sm_spider_num` int(11) DEFAULT NULL;
ALTER TABLE `mip_articles` ADD `sogou_spider_num` int(11) DEFAULT NULL;
ALTER TABLE `mip_articles` ADD `baidu_spider_time` int(11) DEFAULT NULL;
ALTER TABLE `mip_articles` ADD `google_spider_time` int(11) DEFAULT NULL;
ALTER TABLE `mip_articles` ADD `so_spider_time` int(11) DEFAULT NULL;
ALTER TABLE `mip_articles` ADD `sm_spider_time` int(11) DEFAULT NULL;
ALTER TABLE `mip_articles` ADD `sogou_spider_time` int(11) DEFAULT NULL;


INSERT INTO `mip_settings` VALUES ('69', 'topStatus', '');
INSERT INTO `mip_settings` VALUES ('70', 'productModelUrl', 'product');
INSERT INTO `mip_settings` VALUES ('71', 'productModelName', '产品');




-- ----------------------------
--  Table structure for `mip_articles_table`
-- ----------------------------
DROP TABLE IF EXISTS `mip_articles_table`;
CREATE TABLE `mip_articles_table` (
  `id` char(24) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `value` (`value`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `mip_page`
-- ----------------------------
DROP TABLE IF EXISTS `mip_page`;
CREATE TABLE `mip_page` (
  `id` char(24) CHARACTER SET latin1 NOT NULL,
  `url_name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `content` longtext,
  `site_id` char(24) CHARACTER SET latin1 DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `template` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `mip_product`
-- ----------------------------
DROP TABLE IF EXISTS `mip_product`;
CREATE TABLE `mip_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(24) DEFAULT NULL,
  `cid` int(11) DEFAULT '0',
  `uid` char(24) DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `content` longtext,
  `description` varchar(255) DEFAULT NULL,
  `views` int(11) DEFAULT '0',
  `is_recommend` tinyint(1) DEFAULT '0',
  `comments` int(11) DEFAULT '0',
  `version` int(10) DEFAULT '0',
  `edit_time` int(11) DEFAULT '0',
  `publish_time` int(11) DEFAULT '0',
  `url_name` varchar(255) DEFAULT NULL,
  `img_url` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `link_tags` varchar(255) DEFAULT NULL,
  `mip_push_num` int(11) DEFAULT NULL,
  `amp_push_num` int(11) DEFAULT NULL,
  `xzh_push_num` int(11) DEFAULT NULL,
  `link_push_num` int(11) DEFAULT NULL,
  `yc_push_num` int(11) DEFAULT NULL,
  `baidu_spider_num` int(11) DEFAULT NULL,
  `google_spider_num` int(11) DEFAULT NULL,
  `so_spider_num` int(11) DEFAULT NULL,
  `sm_spider_num` int(11) DEFAULT NULL,
  `sogou_spider_num` int(11) DEFAULT NULL,
  `baidu_spider_time` int(11) DEFAULT NULL,
  `google_spider_time` int(11) DEFAULT NULL,
  `so_spider_time` int(11) DEFAULT NULL,
  `sm_spider_time` int(11) DEFAULT NULL,
  `sogou_spider_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `unid` (`uuid`),
  KEY `cid` (`cid`),
  KEY `uid` (`uid`),
  KEY `publish_time` (`publish_time`),
  KEY `is_recommend` (`is_recommend`),
  KEY `views` (`views`) USING BTREE,
  KEY `baidu_spider_num` (`baidu_spider_num`,`google_spider_num`,`so_spider_num`,`sm_spider_num`,`sogou_spider_num`,`baidu_spider_time`,`google_spider_time`,`so_spider_time`,`sm_spider_time`,`sogou_spider_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `mip_product_category`
-- ----------------------------
DROP TABLE IF EXISTS `mip_product_category`;
CREATE TABLE `mip_product_category` (
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
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `sort` (`sort`),
  KEY `pid` (`pid`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `mip_product_table`
-- ----------------------------
DROP TABLE IF EXISTS `mip_product_table`;
CREATE TABLE `mip_product_table` (
  `id` char(24) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `value` (`value`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 



--
-- 更新当前版本
--
UPDATE `mip_settings` SET `val` = '320' WHERE `key` = 'localCurrentVersionNum';
 
--
-- 更新当前版本副本
--
UPDATE `mip_settings` SET `val` = 'v3.2.0' WHERE `key` = 'localCurrentVersion';
SET FOREIGN_KEY_CHECKS = 1;

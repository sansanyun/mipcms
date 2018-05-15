
-- ----------------------------
--  Table structure for `mip_access_key`
-- ----------------------------
DROP TABLE IF EXISTS `mip_access_key`;
CREATE TABLE `mip_access_key` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '名称',
  `key` varchar(255) DEFAULT NULL COMMENT 'key字段',
  `type` varchar(255) DEFAULT NULL COMMENT '终端使用',
  PRIMARY KEY (`id`),
  KEY `key` (`key`),
  KEY `type` (`type`)
) ENGINE=MyISAM AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `mip_access_key`
-- ----------------------------
BEGIN;
INSERT INTO `mip_access_key` VALUES ('1', 'PC端', 'qFaLOmUGozsURROtxaAqe87vHSlI0LL1', 'pc'), ('2', '移动端', 'qFaLOmUGoz9URROtxasqe87vHSlI0LL2', 'wap'), ('3', 'PC端备用', 'cFaLOmUGoz9URROtxaAqe37vHSlI0LL3', 'pc'), ('4', 'app', 'qFaLOmUGoz9URR4txaAqe87vHSlI0LL4', 'app'), ('5', 'clouds', 'cFaLOmUGoz9URROtxaAqe37vHSlI0LL5', 'clouds');
COMMIT;

-- ----------------------------
--  Table structure for `mip_articles`
-- ----------------------------
DROP TABLE IF EXISTS `mip_articles`;
CREATE TABLE `mip_articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(24) DEFAULT NULL,
  `cid` int(11) NOT NULL DEFAULT '0',
  `uid` char(24) NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `views` int(11) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `edit_time` int(11) NOT NULL DEFAULT '0',
  `is_recommend` tinyint(1) NOT NULL DEFAULT '0',
  `comments` int(11) NOT NULL DEFAULT '0',
  `version` int(10) NOT NULL DEFAULT '0',
  `publish_time` int(11) NOT NULL DEFAULT '0',
  `collect` tinyint(1) NOT NULL DEFAULT '0',
  `mip_post_num` int(11) NOT NULL DEFAULT '0',
  `content_id` char(24) DEFAULT '',
  `url_name` varchar(255) DEFAULT NULL,
  `img_url` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `link_tags` varchar(255) DEFAULT NULL,
  `description` text,
  `site_id` char(24) DEFAULT NULL,
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
  KEY `site_id` (`site_id`),
  KEY `publish_time` (`publish_time`),
  KEY `is_recommend` (`is_recommend`),
  KEY `views` (`views`) USING BTREE,
  KEY `create_time` (`create_time`),
  KEY `baidu_spider_num` (`baidu_spider_num`,`google_spider_num`,`so_spider_num`,`sm_spider_num`,`sogou_spider_num`,`baidu_spider_time`,`google_spider_time`,`so_spider_time`,`sm_spider_time`,`sogou_spider_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
-- ----------------------------
--  Table structure for `mip_articles_category`
-- ----------------------------
DROP TABLE IF EXISTS `mip_articles_category`;
CREATE TABLE `mip_articles_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `pid` tinyint(5) DEFAULT NULL,
  `sort` tinyint(5) DEFAULT '99',
  `keywords` varchar(255) DEFAULT NULL,
  `description` text,
  `icon` varchar(255) DEFAULT NULL,
  `url_name` varchar(255) DEFAULT NULL,
  `count_num` int(11) DEFAULT '0',
  `seo_title` varchar(255) DEFAULT NULL,  
  `template` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `is_page` int(11) NOT NULL DEFAULT '0',
  `category_url` varchar(255) DEFAULT NULL,
  `category_page_url` varchar(255) DEFAULT NULL,
  `detail_url` varchar(255) DEFAULT NULL,
  `detail_template` varchar(255) DEFAULT NULL,
  `content` longtext,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `sort` (`sort`),
  KEY `pid` (`pid`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `mip_articles_comments`
-- ----------------------------
DROP TABLE IF EXISTS `mip_articles_comments`;
CREATE TABLE `mip_articles_comments` (
  `id` char(24) NOT NULL,
  `item_id` char(24) DEFAULT NULL,
  `uid` char(24) DEFAULT NULL,
  `content` text,
  `create_time` int(11) DEFAULT NULL,
  `edit_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `item_id` (`item_id`),
  KEY `uid` (`uid`),
  KEY `create_time` (`create_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `mip_articles_content`
-- ----------------------------
DROP TABLE IF EXISTS `mip_articles_content`;
CREATE TABLE `mip_articles_content` (
  `id` char(24) NOT NULL,
  `content` longtext,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `mip_articles_setting`
-- ----------------------------
DROP TABLE IF EXISTS `mip_articles_setting`;
CREATE TABLE `mip_articles_setting` (
  `key` varchar(255) CHARACTER SET utf8 NOT NULL,
  `val` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`key`),
  KEY `key` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `mip_articles_setting`
-- ----------------------------
BEGIN;
INSERT INTO `mip_articles_setting` VALUES ('articlePublishName', '投稿'), ('articlePublishStatus', '1'), ('articlePublishUserNumDay', '10'), ('articlePublishUserTime', '10'), ('articlePublishUserIntegral', '1'), ('commentsStatus', '1'), ('commentsShowNum', '20'), ('aritcleLevelRemove', null);
COMMIT;

-- ----------------------------
--  Table structure for `mip_item_tags`
-- ----------------------------
DROP TABLE IF EXISTS `mip_item_tags`;
CREATE TABLE `mip_item_tags` (
  `id` char(24) NOT NULL,
  `item_id` char(24) DEFAULT NULL,
  `tags_id` char(24) DEFAULT NULL,
  `item_type` varchar(255) DEFAULT NULL,
  `item_add_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `item_id` (`item_id`),
  KEY `tags_id` (`tags_id`),
  KEY `item_type` (`item_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `mip_roles_access`
-- ----------------------------
DROP TABLE IF EXISTS `mip_roles_access`;
CREATE TABLE `mip_roles_access` (
  `group_id` smallint(6) unsigned NOT NULL DEFAULT '0',
  `node_id` smallint(6) unsigned NOT NULL DEFAULT '0',
  `level` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pid` smallint(6) unsigned NOT NULL DEFAULT '0',
  KEY `groupId` (`group_id`),
  KEY `nodeId` (`node_id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `mip_roles_node`
-- ----------------------------
DROP TABLE IF EXISTS `mip_roles_node`;
CREATE TABLE `mip_roles_node` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `pid` smallint(6) unsigned NOT NULL DEFAULT '0',
  `group_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(50) NOT NULL DEFAULT '',
  `remark` varchar(255) NOT NULL DEFAULT '',
  `level` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '节点类型，1-控制器 | 0-方法',
  `sort` smallint(6) unsigned NOT NULL DEFAULT '50',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `isdelete` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `model_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `level` (`level`),
  KEY `pid` (`pid`),
  KEY `status` (`status`),
  KEY `name` (`name`),
  KEY `isdelete` (`isdelete`),
  KEY `sort` (`sort`),
  KEY `group_id` (`group_id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `mip_settings`
-- ----------------------------
DROP TABLE IF EXISTS `mip_settings`;
CREATE TABLE `mip_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `val` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=69 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `mip_settings`
-- ----------------------------
BEGIN;
INSERT INTO `mip_settings` VALUES ('1', 'siteName', 'MIPCMS内容管理系统'), ('2', 'keywords', ''), ('3', 'description', ''), ('4', 'template', 'default'), ('5', 'domain', ''), ('6', 'uploadPath', ''), ('7', 'uploadUrl', 'uploads'), ('8', 'statistical', ''), ('9', 'icp', ''), ('10', 'systemStatus', '1'), ('11', 'systemType', 'cms'), ('12', 'idStatus', ''), ('13', 'mipDomain', ''), ('14', 'articleModelName', '文章'), ('15', 'loginStatus', '1'), ('16', 'registerStatus', ''), ('17', 'articleModelUrl', 'article'), ('18', 'askModelName', '问答'), ('19', 'askModelUrl', 'ask'), ('20', 'userModelName', '用户'), ('21', 'userModelUrl', 'user'), ('22', 'codeCompression', ''), ('23', 'indexTitle', '-基于百度MIP开发的建站系统'), ('24', 'baiduSpider', '1'), ('25', 'baiduMip', '1'), ('26', 'localCurrentVersionNum', '360'), ('27', 'localCurrentVersion', 'v3.6.0'), ('28', 'titleSeparator', '_'), ('29', 'pcStatistical', ''), ('30', 'httpType', 'http://'), ('31', 'mipApiAddress', ''), ('32', 'articlePages', ''), ('33', 'tagModelName', '标签'), ('34', 'tagModelUrl', 'tag'), ('35', 'loginCaptcha', '1'), ('36', 'registerCaptcha', '1'), ('46', 'biaduZn', '12775452642328057043'), ('37', 'articleStatus', '1'), ('38', 'askStatus', ''), ('39', 'aritcleLevelRemove', ''), ('40', 'askLevelRemove', ''), ('41', 'articleDomain', ''), ('42', 'askDomain', ''), ('43', 'superSites', ''), ('47', 'rewrite', ''), ('44', 'topDomain', ''), ('45', 'superTpl', ''), ('48', 'diyUrlStatus', ''), ('49', 'urlApiAddress', null), ('50', 'mipPostStatus', ''), ('52', 'mipTemplate', 'default'), ('51', 'articlePagesNum', '1000'), ('54', 'urlPageBreak', '_'), ('53', 'urlCategory', ''), ('55', 'baiduSearchPcUrl', 'http:///baiduSitemapPc.xml'), ('56', 'baiduSearchMUrl', 'http:///baiduSitemapMobile.xml'), ('57', 'baiduYuanChuangUrl', null), ('58', 'baiduTimePcUrl', null), ('59', 'baiduTimeMUrl', null), ('60', 'publishTime', null), ('61', 'baiduYuanChuangStatus', ''), ('62', 'baiduTimePcStatus', ''), ('63', 'baiduTimeMStatus', ''), ('64', 'guanfanghaoStatus', ''), ('65', 'guanfanghaoUrl', null), ('66', 'guanfanghaoStatusPost', ''), ('67', 'guanfanghaoCambrian', '<mip-cambrian site-id=\"官方号ID\"></mip-cambrian>\n'), ('68', 'guanfanghaoRealtimeUrl', null), ('69', 'topStatus', null), ('70', 'productModelUrl', 'product'), ('71', 'productModelName', '产品');
COMMIT;

 
-- ----------------------------
--  Table structure for `mip_tags`
-- ----------------------------
DROP TABLE IF EXISTS `mip_tags`;
CREATE TABLE `mip_tags` (
  `id` char(24) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `url_name` varchar(255) DEFAULT NULL,
  `item_type` varchar(255) DEFAULT NULL,
  `relevance_num` int(11) DEFAULT NULL COMMENT '关联数量',
  `creator_uid` char(24) DEFAULT NULL COMMENT '标签的创建者',
  `add_time` int(11) DEFAULT NULL,
  `description` text,
  `cid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `mip_users`
-- ----------------------------
DROP TABLE IF EXISTS `mip_users`;
CREATE TABLE `mip_users` (
  `uid` char(24) NOT NULL,
  `username` varchar(32) DEFAULT NULL COMMENT '用户名',
  `nickname` varchar(32) DEFAULT NULL COMMENT '昵称',
  `password` varchar(32) DEFAULT NULL COMMENT '密码',
  `salt` varchar(10) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL COMMENT '用户邮箱',
  `mobile` varchar(15) DEFAULT NULL COMMENT '用户手机',
  `qq` varchar(13) DEFAULT NULL,
  `sex` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '性别（1-男，2-女）',
  `group_id` tinyint(4) unsigned NOT NULL DEFAULT '2',
  `rank` tinyint(4) DEFAULT NULL COMMENT '等级',
  `login_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `reg_ip` varchar(20) DEFAULT NULL COMMENT '注册IP',
  `reg_time` int(11) DEFAULT NULL COMMENT '注册时间',
  `last_login_ip` varchar(20) DEFAULT NULL COMMENT '最后登录IP',
  `last_login_time` int(11) DEFAULT NULL COMMENT '最后登录时间',
  `friend_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '好友个数',
  `signature` varchar(255) DEFAULT NULL,
  `article_num` int(11) unsigned NOT NULL DEFAULT '0',
  `article_comments_num` int(11) unsigned NOT NULL DEFAULT '0',
  `article_views_num` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '用户状态 1为停止使用',
  `collect` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `terminal` varchar(5) NOT NULL DEFAULT 'pc' COMMENT '用户终端',
  `ask_num` int(11) unsigned NOT NULL DEFAULT '0',
  `ask_answers_num` int(11) unsigned NOT NULL DEFAULT '0',
  `integral` int(11) NOT NULL DEFAULT '0',
  `home_page_views_num` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  KEY `uid` (`uid`) USING BTREE,
  KEY `username` (`username`),
  KEY `group_id` (`group_id`),
  KEY `reg_time` (`reg_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- ----------------------------
--  Table structure for `mip_users_group`
-- ----------------------------
DROP TABLE IF EXISTS `mip_users_group`;
CREATE TABLE `mip_users_group` (
  `group_id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT 'icon小图标',
  `sort` int(11) unsigned NOT NULL DEFAULT '50',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `remark` varchar(255) NOT NULL DEFAULT '',
  `isdelete` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_id`),
  KEY `sort` (`sort`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Records of `mip_users_group`
-- ----------------------------
BEGIN;
INSERT INTO `mip_users_group` VALUES ('1', '超级管理员', '', '1', '1', '', '0', '0', '0'), ('2', '注册会员', '', '2', '1', '', '0', '0', '0'), ('3', 'VIP会员', '', '3', '1', '', '0', '0', '0');
COMMIT;

DROP TABLE IF EXISTS `mip_domain_sites`;
CREATE TABLE `mip_domain_sites` (
  `id` char(24) NOT NULL,
  `http_type` varchar(255) DEFAULT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `template` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `data_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `domain` (`domain`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `mip_domain_settings`;
CREATE TABLE `mip_domain_settings` (
  `id` char(24) CHARACTER SET latin1 NOT NULL,
  `mipApi` varchar(255) DEFAULT NULL,
  `mipAutoStatus` tinyint(1) DEFAULT '0',
  `ampApi` varchar(255) DEFAULT NULL,
  `ampAutoStatus` tinyint(1) DEFAULT '0',
  `xiongZhangStatus` tinyint(1) DEFAULT '0',
  `xiongZhangId` varchar(255) DEFAULT NULL,
  `xiongZhangNewApi` varchar(255) DEFAULT NULL,
  `xiongZhangNewAutoStatus` tinyint(1) DEFAULT '0',
  `xiongZhangOldApi` varchar(255) DEFAULT NULL,
  `yuanChuangApi` varchar(255) DEFAULT NULL,
  `yuanChuangAutoStatus` tinyint(1) DEFAULT '0',
  `linkApi` varchar(255) DEFAULT NULL,
  `linkAutoStatus` tinyint(1) DEFAULT '0',
  `baiduSearchKey` varchar(255) DEFAULT NULL,
  `baiduSearchSiteMap` varchar(255) DEFAULT NULL,
  `siteName` varchar(255) DEFAULT NULL,
  `indexTitle` varchar(255) DEFAULT NULL,
  `diySiteName` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `description` text,
  `icp` varchar(255) DEFAULT NULL,
  `statistical` text,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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

DROP TABLE IF EXISTS `mip_tags_category`;
CREATE TABLE `mip_tags_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `pid` tinyint(5) DEFAULT NULL,
  `sort` tinyint(5) DEFAULT '99',
  `keywords` varchar(255) DEFAULT NULL,
  `description` text,
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

DROP TABLE IF EXISTS `mip_key`;
CREATE TABLE `mip_key` (
  `key` varchar(255) NOT NULL,
  `val` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

BEGIN;
INSERT INTO `mip_key` VALUES ('baiduXZClientId', ''), ('baiduXZClientSecret', '') ,('baiduXZToken', ''),('baiduXZRedirectUri', '');
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
SET FOREIGN_KEY_CHECKS = 1;

DROP TABLE IF EXISTS `mip_addons`;
CREATE TABLE `mip_addons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


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




/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50714
Source Host           : localhost:3306
Source Database       : mipcms

Target Server Type    : MYSQL
Target Server Version : 50714
File Encoding         : 65001

Date: 2017-06-08 01:38:13
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for mip_access_key
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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mip_access_key
-- ----------------------------
INSERT INTO `mip_access_key` VALUES ('1', 'PC端', 'qFaLOmUGozsURROtxaAqe87vHSlI0LL1', 'pc');
INSERT INTO `mip_access_key` VALUES ('2', '移动端', 'qFaLOmUGoz9URROtxasqe87vHSlI0LL2', 'wap');
INSERT INTO `mip_access_key` VALUES ('3', 'PC端备用', 'cFaLOmUGoz9URROtxaAqe37vHSlI0LL3', 'pc');
INSERT INTO `mip_access_key` VALUES ('4', 'app', 'qFaLOmUGoz9URR4txaAqe87vHSlI0LL4', 'app');

-- ----------------------------
-- Table structure for mip_articles
-- ----------------------------
DROP TABLE IF EXISTS `mip_articles`;
CREATE TABLE `mip_articles` (
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
  `mip_post_num` int(11) NOT NULL DEFAULT '0',
  `content_id` char(24) DEFAULT NULL,
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
-- Records of mip_articles
-- ----------------------------

-- ----------------------------
-- Table structure for mip_articles_approval
-- ----------------------------
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

-- ----------------------------
-- Records of mip_articles_approval
-- ----------------------------

-- ----------------------------
-- Table structure for mip_articles_category
-- ----------------------------
DROP TABLE IF EXISTS `mip_articles_category`;
CREATE TABLE `mip_articles_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `pid` tinyint(5) DEFAULT NULL,
  `sort` tinyint(5) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `url_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `sort` (`sort`),
  KEY `pid` (`pid`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mip_articles_category
-- ----------------------------

-- ----------------------------
-- Table structure for mip_articles_comments
-- ----------------------------
DROP TABLE IF EXISTS `mip_articles_comments`;
CREATE TABLE `mip_articles_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
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
-- Records of mip_articles_comments
-- ----------------------------

-- ----------------------------
-- Table structure for mip_articles_content
-- ----------------------------
DROP TABLE IF EXISTS `mip_articles_content`;
CREATE TABLE `mip_articles_content` (
  `id` char(24) NOT NULL,
  `content` longtext,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mip_articles_content
-- ----------------------------

-- ----------------------------
-- Table structure for mip_articles_draft
-- ----------------------------
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
-- Records of mip_articles_draft
-- ----------------------------

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

-- ----------------------------
-- Table structure for mip_asks
-- ----------------------------
DROP TABLE IF EXISTS `mip_asks`;
CREATE TABLE `mip_asks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(24) DEFAULT NULL,
  `cid` int(11) unsigned DEFAULT '0',
  `uid` int(11) unsigned DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `views` int(11) unsigned DEFAULT '0',
  `publish_time` int(11) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `edit_time` int(11) DEFAULT NULL,
  `anonymous` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '匿名',
  `is_recommend` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `reward` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '悬赏分',
  `expire_time` int(11) DEFAULT NULL COMMENT '到期时间',
  `solve_time` int(11) DEFAULT NULL COMMENT '解决时间',
  `best_answer` int(11) unsigned DEFAULT '0' COMMENT '最佳答案',
  `answer_num` int(11) unsigned NOT NULL DEFAULT '0',
  `collect` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `collectUrl` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `publish_time` (`publish_time`),
  KEY `views` (`views`),
  KEY `uuid` (`uuid`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mip_asks
-- ----------------------------

-- ----------------------------
-- Table structure for mip_asks_answer
-- ----------------------------
DROP TABLE IF EXISTS `mip_asks_answer`;
CREATE TABLE `mip_asks_answer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `content` text,
  `ask_id` int(11) unsigned NOT NULL DEFAULT '0',
  `is_answer` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为最佳答案',
  `anonymous` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否匿名',
  `create_time` int(11) DEFAULT NULL COMMENT '回复时间',
  `is_check` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否审核',
  `comment_num` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mip_asks_answer
-- ----------------------------

-- ----------------------------
-- Table structure for mip_asks_category
-- ----------------------------
DROP TABLE IF EXISTS `mip_asks_category`;
CREATE TABLE `mip_asks_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `pid` tinyint(5) DEFAULT NULL,
  `sort` tinyint(5) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `url_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mip_asks_category
-- ----------------------------

-- ----------------------------
-- Table structure for mip_friendlink
-- ----------------------------
DROP TABLE IF EXISTS `mip_friendlink`;
CREATE TABLE `mip_friendlink` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `sort` int(5) DEFAULT '999',
  `type` varchar(100) DEFAULT 'all',
  `add_time` int(11) unsigned zerofill NOT NULL,
  `status` int(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `name` (`name`),
  KEY `sort` (`sort`),
  KEY `add_time` (`add_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mip_friendlink
-- ----------------------------

-- ----------------------------
-- Table structure for mip_item_tags
-- ----------------------------
DROP TABLE IF EXISTS `mip_item_tags`;
CREATE TABLE `mip_item_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) unsigned zerofill DEFAULT NULL,
  `tags_id` int(11) unsigned zerofill DEFAULT NULL,
  `item_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `item_id` (`item_id`),
  KEY `tags_id` (`tags_id`),
  KEY `item_type` (`item_type`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mip_item_tags
-- ----------------------------
INSERT INTO `mip_item_tags` VALUES ('1', '00000015828', '00000000001', 'article');
INSERT INTO `mip_item_tags` VALUES ('2', '00000015829', '00000000001', 'article');
INSERT INTO `mip_item_tags` VALUES ('3', '00000015830', '00000000001', 'article');

-- ----------------------------
-- Table structure for mip_roles_access
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
-- Records of mip_roles_access
-- ----------------------------

-- ----------------------------
-- Table structure for mip_roles_node
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
-- Records of mip_roles_node
-- ----------------------------

-- ----------------------------
-- Table structure for mip_settings
-- ----------------------------
DROP TABLE IF EXISTS `mip_settings`;
CREATE TABLE `mip_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `val` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mip_settings
-- ----------------------------
INSERT INTO `mip_settings` VALUES ('1', 'siteName', '我的网站');
INSERT INTO `mip_settings` VALUES ('2', 'keywords', '');
INSERT INTO `mip_settings` VALUES ('3', 'description', '');
INSERT INTO `mip_settings` VALUES ('4', 'template', 'default');
INSERT INTO `mip_settings` VALUES ('5', 'domain', '');
INSERT INTO `mip_settings` VALUES ('6', 'uploadPath', '');
INSERT INTO `mip_settings` VALUES ('7', 'uploadUrl', 'uploads');
INSERT INTO `mip_settings` VALUES ('8', 'statistical', '');
INSERT INTO `mip_settings` VALUES ('9', 'icp', '');
INSERT INTO `mip_settings` VALUES ('10', 'systemStatus', '1');
INSERT INTO `mip_settings` VALUES ('11', 'systemType', 'CMS');
INSERT INTO `mip_settings` VALUES ('12', 'idStatus', '');
INSERT INTO `mip_settings` VALUES ('13', 'mipDomain', '');
INSERT INTO `mip_settings` VALUES ('14', 'articleModelName', '文章');
INSERT INTO `mip_settings` VALUES ('15', 'loginStatus', '1');
INSERT INTO `mip_settings` VALUES ('16', 'registerStatus', '1');
INSERT INTO `mip_settings` VALUES ('17', 'articleModelUrl', 'article');
INSERT INTO `mip_settings` VALUES ('18', 'askModelName', '问答');
INSERT INTO `mip_settings` VALUES ('19', 'askModelUrl', 'ask');
INSERT INTO `mip_settings` VALUES ('20', 'userModelName', '用户');
INSERT INTO `mip_settings` VALUES ('21', 'userModelUrl', 'user');
INSERT INTO `mip_settings` VALUES ('22', 'codeCompression', '');
INSERT INTO `mip_settings` VALUES ('23', 'indexTitle', '-这个是网站的副标题');
INSERT INTO `mip_settings` VALUES ('24', 'baiduSpider', '1');
INSERT INTO `mip_settings` VALUES ('25', 'baiduMip', '1');
INSERT INTO `mip_settings` VALUES ('26', 'localCurrentVersionNum', '200');
INSERT INTO `mip_settings` VALUES ('27', 'localCurrentVersion', 'v2.0.0');
INSERT INTO `mip_settings` VALUES ('28', 'titleSeparator', '_');
INSERT INTO `mip_settings` VALUES ('29', 'pcStatistical', '');
INSERT INTO `mip_settings` VALUES ('30', 'https', 'http');
INSERT INTO `mip_settings` VALUES ('31', 'mipApiAddress', '');
INSERT INTO `mip_settings` VALUES ('32', 'articlePages', '');

-- ----------------------------
-- Table structure for mip_spiders
-- ----------------------------
DROP TABLE IF EXISTS `mip_spiders`;
CREATE TABLE `mip_spiders` (
  `uuid` char(24) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `add_time` int(11) unsigned zerofill NOT NULL,
  `pageUrl` varchar(255) NOT NULL,
  `ua` varchar(255) DEFAULT NULL,
  `vendor` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`uuid`),
  KEY `uuid` (`uuid`),
  KEY `add_time` (`add_time`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mip_spiders
-- ----------------------------

-- ----------------------------
-- Table structure for mip_tags
-- ----------------------------
DROP TABLE IF EXISTS `mip_tags`;
CREATE TABLE `mip_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `url_name` varchar(255) DEFAULT NULL,
  `item_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mip_tags
-- ----------------------------

-- ----------------------------
-- Table structure for mip_users
-- ----------------------------
DROP TABLE IF EXISTS `mip_users`;
CREATE TABLE `mip_users` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(24) DEFAULT NULL,
  `username` varchar(16) DEFAULT NULL COMMENT '用户名',
  `nickname` varchar(16) DEFAULT NULL COMMENT '昵称',
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
  PRIMARY KEY (`uid`),
  KEY `uid` (`uid`) USING BTREE,
  KEY `username` (`username`),
  KEY `group_id` (`group_id`),
  KEY `reg_time` (`reg_time`),
  KEY `uuid` (`uuid`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mip_users
-- ----------------------------
INSERT INTO `mip_users` VALUES ('1', '4e585f0a641dbb44db77096e', 'admin', '', '270040f5e07f5a5b0be843222c399aeb', '68fecd', '', '', '', '1', '1', '1', '18', '0.0.0.0', '0', '0.0.0.0', '1496855474', '0', '', '2', '1', '10', '0', '0', 'pc');

-- ----------------------------
-- Table structure for mip_users_group
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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of mip_users_group
-- ----------------------------
INSERT INTO `mip_users_group` VALUES ('1', '超级管理员', '', '1', '1', '', '0', '0', '0');
INSERT INTO `mip_users_group` VALUES ('2', '注册会员', '', '2', '1', '', '0', '0', '0');
INSERT INTO `mip_users_group` VALUES ('3', 'VIP会员', '', '3', '1', '', '0', '0', '0');
INSERT INTO `mip_users_group` VALUES ('4', '临时分组', '', '4', '1', '', '0', '0', '0');

/*
 Navicat MySQL Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 100110
 Source Host           : localhost
 Source Database       : mipcms

 Target Server Type    : MySQL
 Target Server Version : 100110
 File Encoding         : utf-8

 Date: 07/12/2017 22:57:55 PM
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `mip_access_key`
-- ----------------------------
BEGIN;
INSERT INTO `mip_access_key` VALUES ('1', 'PC端', 'qFaLOmUGozsURROtxaAqe87vHSlI0LL1', 'pc'), ('2', '移动端', 'qFaLOmUGoz9URROtxasqe87vHSlI0LL2', 'wap'), ('3', 'PC端备用', 'cFaLOmUGoz9URROtxaAqe37vHSlI0LL3', 'pc'), ('4', 'app', 'qFaLOmUGoz9URR4txaAqe87vHSlI0LL4', 'app');
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
--  Table structure for `mip_articles_approval`
-- ----------------------------
DROP TABLE IF EXISTS `mip_articles_approval`;
CREATE TABLE `mip_articles_approval` (
  `id` char(24) NOT NULL,
  `cid` int(11) DEFAULT '0',
  `uid` char(24) DEFAULT '0',
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
  KEY `cid` (`cid`),
  KEY `uid` (`uid`),
  KEY `publish_time` (`publish_time`),
  KEY `is_recommend` (`is_recommend`),
  KEY `views` (`views`) USING BTREE,
  KEY `create_time` (`create_time`)
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
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

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
--  Table structure for `mip_articles_draft`
-- ----------------------------
DROP TABLE IF EXISTS `mip_articles_draft`;
CREATE TABLE `mip_articles_draft` (
  `id` char(24) NOT NULL,
  `cid` int(11) unsigned DEFAULT '0',
  `uid` char(24) DEFAULT '0',
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
  KEY `cid` (`cid`),
  KEY `uid` (`uid`),
  KEY `publish_time` (`publish_time`),
  KEY `is_recommend` (`is_recommend`),
  KEY `views` (`views`) USING BTREE,
  KEY `create_time` (`create_time`)
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
--  Records of `mip_articles_setting`
-- ----------------------------
BEGIN;
INSERT INTO `mip_articles_setting` VALUES ('articlePublishName', '投稿'), ('articlePublishStatus', '1'), ('articlePublishUserNumDay', '10'), ('articlePublishUserTime', '10'), ('articlePublishUserIntegral', '1'), ('commentsStatus', '1'), ('commentsShowNum', '20'), ('aritcleLevelRemove', null);
COMMIT;

-- ----------------------------
--  Table structure for `mip_asks`
-- ----------------------------
DROP TABLE IF EXISTS `mip_asks`;
CREATE TABLE `mip_asks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(24) DEFAULT NULL,
  `cid` int(11) unsigned DEFAULT '0',
  `uid` char(24) DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
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
  `content_id` char(24) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `publish_time` (`publish_time`),
  KEY `views` (`views`),
  KEY `uuid` (`uuid`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `mip_asks_answers`
-- ----------------------------
DROP TABLE IF EXISTS `mip_asks_answers`;
CREATE TABLE `mip_asks_answers` (
  `id` char(24) NOT NULL,
  `uid` char(24) DEFAULT '0',
  `content` text,
  `item_id` char(24) DEFAULT '0',
  `is_answer` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为最佳答案',
  `anonymous` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否匿名',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '回复时间',
  `is_check` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否审核',
  `comment_num` int(11) NOT NULL DEFAULT '0',
  `edit_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=42 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `mip_asks_answers_comments`
-- ----------------------------
DROP TABLE IF EXISTS `mip_asks_answers_comments`;
CREATE TABLE `mip_asks_answers_comments` (
  `id` char(24) NOT NULL,
  `item_id` char(24) DEFAULT NULL,
  `uid` char(24) DEFAULT NULL,
  `content` text,
  `create_time` int(11) DEFAULT NULL,
  `edit_time` int(11) DEFAULT NULL,
  `is_reply` int(1) unsigned DEFAULT '0',
  `reply_uid` char(24) DEFAULT NULL,
  `reply_item_id` char(24) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `item_id` (`item_id`),
  KEY `uid` (`uid`),
  KEY `create_time` (`create_time`)
) ENGINE=MyISAM AUTO_INCREMENT=92 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `mip_asks_category`
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
  `count_num` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `sort` (`sort`),
  KEY `pid` (`pid`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `mip_asks_content`
-- ----------------------------
DROP TABLE IF EXISTS `mip_asks_content`;
CREATE TABLE `mip_asks_content` (
  `id` char(24) NOT NULL,
  `content` longtext,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `mip_asks_setting`
-- ----------------------------
DROP TABLE IF EXISTS `mip_asks_setting`;
CREATE TABLE `mip_asks_setting` (
  `key` varchar(255) CHARACTER SET utf8 NOT NULL,
  `val` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`key`),
  KEY `key` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
--  Records of `mip_asks_setting`
-- ----------------------------
BEGIN;
INSERT INTO `mip_asks_setting` VALUES ('askPublishName', '提问'), ('askPublishStatus', '1'), ('askPublishUserNumDay', '15'), ('askPublishUserTime', '5'), ('askPublishUserIntegral', '1'), ('answerStatus', '1'), ('answerShowNum', '20'), ('askDomain', null), ('answerUserNum', '1');
COMMIT;

-- ----------------------------
--  Table structure for `mip_friendlink`
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
) ENGINE=MyISAM AUTO_INCREMENT=63 DEFAULT CHARSET=utf8;

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
--  Records of `mip_roles_access`
-- ----------------------------
BEGIN;
INSERT INTO `mip_roles_access` VALUES ('2', '1', '1', '0'), ('2', '2', '2', '1'), ('2', '3', '3', '2'), ('2', '4', '3', '2'), ('1', '1', '1', '0'), ('1', '2', '2', '1'), ('1', '3', '3', '2'), ('1', '4', '3', '2');
COMMIT;

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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Records of `mip_roles_node`
-- ----------------------------
BEGIN;
INSERT INTO `mip_roles_node` VALUES ('1', '0', '0', 'ask', '问答模块', '', '1', '1', '50', '1', '0', null), ('2', '1', '0', 'answer', '回答', '', '2', '1', '50', '1', '0', null), ('3', '2', '0', 'answerDel', '回答删除', '', '3', '1', '50', '1', '0', null), ('4', '2', '0', 'answerEdit', '回答编辑', '', '3', '1', '50', '1', '0', null);
COMMIT;

-- ----------------------------
--  Table structure for `mip_settings`
-- ----------------------------
DROP TABLE IF EXISTS `mip_settings`;
CREATE TABLE `mip_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `val` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `mip_settings`
-- ----------------------------
BEGIN;
INSERT INTO `mip_settings` VALUES ('1', 'siteName', '我的网站'), ('2', 'keywords', ''), ('3', 'description', ''), ('4', 'template', 'default'), ('5', 'domain', ''), ('6', 'uploadPath', ''), ('7', 'uploadUrl', 'uploads'), ('8', 'statistical', ''), ('9', 'icp', ''), ('10', 'systemStatus', '1'), ('11', 'systemType', 'CMS'), ('12', 'idStatus', ''), ('13', 'mipDomain', ''), ('14', 'articleModelName', '文章'), ('15', 'loginStatus', '1'), ('16', 'registerStatus', ''), ('17', 'articleModelUrl', 'article'), ('18', 'askModelName', '问答'), ('19', 'askModelUrl', 'ask'), ('20', 'userModelName', '用户'), ('21', 'userModelUrl', 'user'), ('22', 'codeCompression', ''), ('23', 'indexTitle', '-这个是网站的副标题'), ('24', 'baiduSpider', '1'), ('25', 'baiduMip', '1'), ('26', 'localCurrentVersionNum', '210'), ('27', 'localCurrentVersion', 'v2.1.0'), ('28', 'titleSeparator', '_'), ('29', 'pcStatistical', ''), ('30', 'httpType', 'http://'), ('31', 'mipApiAddress', ''), ('32', 'articlePages', ''), ('33', 'tagModelName', '标签'), ('34', 'tagModelUrl', 'tag'), ('35', 'loginCaptcha', null), ('36', 'registerCaptcha', null), ('46', 'biaduZn', '12775452642328057043'), ('37', 'articleStatus', '1'), ('38', 'askStatus', ''), ('39', 'aritcleLevelRemove', ''), ('40', 'askLevelRemove', ''), ('41', 'articleDomain', ''), ('42', 'askDomain', ''), ('43', 'superSites', ''), ('47', 'rewrite', ''), ('44', 'topDomain', 'test.com'), ('45', 'superTpl', ''), ('48', 'diyUrlStatus', ''), ('49', 'urlApiAddress', null), ('50', 'mipPostStatus', ''), ('52', 'mipTemplate', 'default'), ('51', 'articlePagesNum', '1000');
COMMIT;

-- ----------------------------
--  Table structure for `mip_spiders`
-- ----------------------------
DROP TABLE IF EXISTS `mip_spiders`;
CREATE TABLE `mip_spiders` (
  `uuid` char(24) NOT NULL,
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
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM AUTO_INCREMENT=1181 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `mip_users`
-- ----------------------------
BEGIN;
INSERT INTO `mip_users` VALUES ('1', 'admin', '', '270040f5e07f5a5b0be843222c399aeb', '68fecd', '', '', '', '1', '1', '1', '51', '0.0.0.0', '0', '127.0.0.1', '1499870263', '0', '这个是admin的签名', '5', '1', '10', '0', '0', 'pc', '12', '3', '0', '30');
COMMIT;

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

SET FOREIGN_KEY_CHECKS = 1;

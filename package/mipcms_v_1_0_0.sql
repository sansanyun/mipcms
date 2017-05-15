-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2017-05-01 09:20:34
-- 服务器版本： 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mipcms`
--

-- --------------------------------------------------------

--
-- 表的结构 `mip_access_key`
--

CREATE TABLE IF NOT EXISTS `mip_access_key` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '名称',
  `key` varchar(255) DEFAULT NULL COMMENT 'key字段',
  `type` varchar(255) DEFAULT NULL COMMENT '终端使用',
  PRIMARY KEY (`id`),
  KEY `key` (`key`),
  KEY `type` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `mip_access_key`
--

INSERT INTO `mip_access_key` (`id`, `name`, `key`, `type`) VALUES
(1, 'PC端', 'qFaLOmUGozsURROtxaAqe87vHSlI0LL1', 'pc'),
(2, '移动端', 'qFaLOmUGoz9URROtxasqe87vHSlI0LL2', 'wap'),
(3, 'PC端备用', 'cFaLOmUGoz9URROtxaAqe37vHSlI0LL3', 'pc'),
(4, 'app', 'qFaLOmUGoz9URR4txaAqe87vHSlI0LL4', 'app');

-- --------------------------------------------------------

--
-- 表的结构 `mip_articles`
--

CREATE TABLE IF NOT EXISTS `mip_articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(24) NOT NULL,
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mip_articles_category`
--

CREATE TABLE IF NOT EXISTS `mip_articles_category` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mip_articles_comments`
--

CREATE TABLE IF NOT EXISTS `mip_articles_comments` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mip_asks`
--

CREATE TABLE IF NOT EXISTS `mip_asks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(24) NOT NULL,
  `cid` int(11) unsigned NOT NULL DEFAULT '0',
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `views` int(11) unsigned NOT NULL DEFAULT '0',
  `publish_time` int(11) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `edit_time` int(11) DEFAULT NULL,
  `anonymous` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_recommend` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `reward` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '悬赏分',
  `expire_time` int(11) DEFAULT NULL COMMENT '到期时间',
  `solve_time` int(11) DEFAULT NULL COMMENT '解决时间',
  `best_answer` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '最佳答案',
  `answer_num` int(11) unsigned NOT NULL DEFAULT '0',
  `collect` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `collectUrl` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `publish_time` (`publish_time`),
  KEY `views` (`views`),
  KEY `uuid` (`uuid`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mip_asks_answer`
--

CREATE TABLE IF NOT EXISTS `mip_asks_answer` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mip_asks_category`
--

CREATE TABLE IF NOT EXISTS `mip_asks_category` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mip_friendlink`
--

CREATE TABLE IF NOT EXISTS `mip_friendlink` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mip_item_tags`
--

CREATE TABLE IF NOT EXISTS `mip_item_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) unsigned zerofill DEFAULT NULL,
  `tags_id` int(11) unsigned zerofill DEFAULT NULL,
  `item_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `item_id` (`item_id`),
  KEY `tags_id` (`tags_id`),
  KEY `item_type` (`item_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mip_roles_access`
--

CREATE TABLE IF NOT EXISTS `mip_roles_access` (
  `group_id` smallint(6) unsigned NOT NULL DEFAULT '0',
  `node_id` smallint(6) unsigned NOT NULL DEFAULT '0',
  `level` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pid` smallint(6) unsigned NOT NULL DEFAULT '0',
  KEY `groupId` (`group_id`),
  KEY `nodeId` (`node_id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- 转存表中的数据 `mip_roles_access`
--

INSERT INTO `mip_roles_access` (`group_id`, `node_id`, `level`, `pid`) VALUES
(1, 50, 3, 49),
(1, 49, 2, 2),
(4, 17, 3, 14),
(4, 15, 3, 14),
(4, 19, 3, 8),
(4, 12, 3, 8),
(4, 10, 3, 8),
(4, 5, 3, 4),
(1, 48, 3, 47),
(1, 47, 2, 2),
(1, 45, 3, 43),
(1, 44, 3, 43),
(1, 43, 2, 2),
(1, 42, 3, 36),
(1, 41, 3, 36),
(1, 40, 3, 36),
(1, 39, 3, 36),
(1, 38, 3, 36),
(1, 37, 3, 36),
(1, 36, 2, 2),
(1, 35, 3, 21),
(1, 34, 3, 21),
(1, 33, 3, 21),
(1, 32, 3, 21),
(1, 31, 3, 21),
(1, 30, 3, 21),
(1, 29, 3, 21),
(1, 28, 3, 21),
(1, 27, 3, 21),
(1, 26, 3, 21),
(1, 25, 3, 21),
(1, 24, 3, 21),
(1, 23, 3, 21),
(1, 22, 3, 21),
(1, 21, 2, 2),
(1, 18, 3, 14),
(1, 17, 3, 14),
(1, 16, 3, 14),
(1, 15, 3, 14),
(2, 32, 3, 21),
(2, 30, 3, 21),
(2, 29, 3, 21),
(2, 28, 3, 21),
(2, 27, 3, 21),
(2, 26, 3, 21),
(2, 25, 3, 21),
(2, 24, 3, 21),
(2, 22, 3, 21),
(1, 14, 2, 2),
(1, 20, 3, 8),
(1, 19, 3, 8),
(1, 13, 3, 8),
(1, 12, 3, 8),
(1, 11, 3, 8),
(1, 10, 3, 8),
(1, 8, 2, 2),
(1, 46, 3, 4),
(1, 7, 3, 4),
(1, 6, 3, 4),
(1, 5, 3, 4),
(1, 4, 2, 2),
(1, 2, 1, 0),
(1, 1, 1, 0),
(1, 51, 3, 49),
(2, 36, 2, 2),
(2, 37, 3, 36),
(2, 38, 3, 36),
(2, 39, 3, 36),
(2, 40, 3, 36),
(2, 41, 3, 36),
(2, 42, 3, 36),
(2, 47, 2, 2),
(2, 48, 3, 47);

-- --------------------------------------------------------

--
-- 表的结构 `mip_roles_node`
--

CREATE TABLE IF NOT EXISTS `mip_roles_node` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=52 ;

--
-- 转存表中的数据 `mip_roles_node`
--

INSERT INTO `mip_roles_node` (`id`, `pid`, `group_id`, `name`, `title`, `remark`, `level`, `type`, `sort`, `status`, `isdelete`) VALUES
(1, 0, 1, 'admin', '后台管理', '', 1, 1, 1, 1, 0),
(2, 0, 1, 'api', 'API权限', ' ', 1, 1, 1, 1, 0),
(4, 2, 0, 'User', '用户管理', '', 2, 1, 50, 1, 0),
(5, 4, 0, 'userCreate', '添加用户', '', 3, 1, 50, 1, 0),
(6, 4, 0, 'userDel', '用户删除', '', 3, 1, 50, 1, 0),
(7, 4, 0, 'userEdit', '用户修改', '', 3, 1, 50, 1, 0),
(8, 2, 0, 'Role', '权限管理', '', 2, 1, 50, 1, 0),
(10, 8, 0, 'rolesNodeAdd', '节点添加', '', 3, 1, 50, 1, 0),
(11, 8, 0, 'rolesNodeDel', '节点删除', '', 3, 1, 50, 1, 0),
(12, 8, 0, 'rolesNodeSelect', '节点查看', '', 3, 1, 50, 1, 0),
(13, 8, 0, 'rolesNodeEdit', '节点修改', '', 3, 1, 50, 1, 0),
(14, 2, 0, 'UserGroup', '分组管理', '', 2, 1, 50, 1, 0),
(15, 14, 0, 'userGroupAdd', '分组添加', '', 3, 1, 50, 1, 0),
(16, 14, 0, 'userGroupDel', '分组删除', '', 3, 1, 50, 1, 0),
(17, 14, 0, 'userGroupSelect', '分组查看', '', 3, 1, 50, 1, 0),
(18, 14, 0, 'userGroupEdit', '分组修改', '', 3, 1, 50, 1, 0),
(19, 8, 0, 'rolesAccessSelect', '权限查看', '', 3, 1, 50, 1, 0),
(20, 8, 0, 'rolesAccessAdd', '权限授权', '', 3, 1, 50, 1, 0),
(21, 2, 0, 'article', '文章管理', '', 2, 1, 50, 1, 0),
(22, 21, 0, 'articleAdd', '文章添加', '', 3, 1, 50, 1, 0),
(23, 21, 0, 'articleDel', '文章删除', '', 3, 1, 50, 1, 0),
(24, 21, 0, 'articlesSelect', '文章查询', '', 3, 1, 50, 1, 0),
(25, 21, 0, 'articleEdit', '文章修改', '', 3, 1, 50, 1, 0),
(26, 21, 0, 'commentsAdd', '回复添加', '', 3, 1, 50, 1, 0),
(27, 21, 0, 'commentDel', '回复删除', '', 3, 1, 50, 1, 0),
(28, 21, 0, 'commentsSelect', '回复查询', '', 3, 1, 50, 1, 0),
(29, 21, 0, 'commentsEdit', '回复修改', '', 3, 1, 50, 1, 0),
(30, 21, 0, 'categoryAdd', '分类添加', '', 3, 1, 50, 1, 0),
(31, 21, 0, 'categoryDel', '分类删除', '', 3, 1, 50, 1, 0),
(32, 21, 0, 'categorySelect', '分类查询', '', 3, 1, 50, 1, 0),
(33, 21, 0, 'categoryEdit', '分类修改', '', 3, 1, 50, 1, 0),
(34, 21, 0, 'articlesDel', '文章删除（批量）', '', 3, 1, 50, 1, 0),
(35, 21, 0, 'commentsDel', '回复删除（批量）', '', 3, 1, 50, 1, 0),
(36, 2, 0, 'tag', '标签管理', '', 2, 1, 50, 1, 0),
(37, 36, 0, 'tagAdd', '标签添加', '', 3, 1, 50, 1, 0),
(38, 36, 0, 'tagsAdd', '标签添加（批量）', '', 3, 1, 50, 1, 0),
(39, 36, 0, 'tagsDel', '标签删除', '', 3, 1, 50, 1, 0),
(40, 36, 0, 'tagsSelect', '标签查询', '', 3, 1, 50, 1, 0),
(41, 36, 0, 'tagsEdit', '标签修改', '', 3, 1, 50, 1, 0),
(42, 36, 0, 'itemTagsSelectByItem', '标签查询（项目）', '', 3, 1, 50, 1, 0),
(43, 2, 0, 'Setting', '系统设置', '', 2, 1, 50, 1, 0),
(44, 43, 0, 'settingSelect', '系统信息查询', '', 3, 1, 50, 1, 0),
(45, 43, 0, 'settingEdit', '系统信息修改', '', 3, 1, 50, 1, 0),
(46, 4, 0, 'usersSelect', '用户查看', '', 3, 1, 50, 1, 0),
(47, 2, 0, 'Upload', '文件上传', '', 2, 1, 50, 1, 0),
(48, 47, 0, 'imgUpload', '图片上传', '', 3, 1, 50, 1, 0),
(49, 2, 0, 'Spider', '搜索引擎', '', 2, 1, 50, 1, 0),
(50, 49, 0, 'spidersSelect', '数据查询', '', 3, 1, 50, 1, 0),
(51, 49, 0, 'spidersToday', '今日统计图', '', 3, 1, 50, 1, 0);

-- --------------------------------------------------------

--
-- 表的结构 `mip_settings`
--

CREATE TABLE IF NOT EXISTS `mip_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `val` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- 转存表中的数据 `mip_settings`
--

INSERT INTO `mip_settings` (`id`, `key`, `val`) VALUES
(1, 'siteName', '我的网站'),
(2, 'keywords', '我的网站关键词'),
(3, 'description', '我的网站描述'),
(4, 'template', 'default'),
(5, 'domain', ''),
(6, 'uploadPath', ''),
(7, 'uploadUrl', 'uploads'),
(8, 'statistical', ''),
(9, 'icp', ''),
(10, 'systemStatus', '1'),
(11, 'systemType', 'Blog'),
(12, 'idStatus', '1'),
(13, 'mipDomain', ''),
(14, 'articleModelName', '文章'),
(15, 'loginStatus', '1'),
(16, 'registerStatus', '1'),
(17, 'articleModelUrl', 'article'),
(18, 'askModelName', '问答');

-- --------------------------------------------------------

--
-- 表的结构 `mip_spiders`
--

CREATE TABLE IF NOT EXISTS `mip_spiders` (
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

-- --------------------------------------------------------

--
-- 表的结构 `mip_tags`
--

CREATE TABLE IF NOT EXISTS `mip_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `url_name` varchar(255) DEFAULT NULL,
  `item_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `mip_users`
--

CREATE TABLE IF NOT EXISTS `mip_users` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(24) NOT NULL,
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
  `article_num` tinyint(11) unsigned NOT NULL DEFAULT '0',
  `article_comments_num` tinyint(11) unsigned NOT NULL DEFAULT '0',
  `article_views_num` tinyint(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '用户状态 1为停止使用',
  `collect` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `terminal` varchar(5) NOT NULL DEFAULT 'pc' COMMENT '用户终端',
  PRIMARY KEY (`uid`),
  KEY `uid` (`uid`) USING BTREE,
  KEY `username` (`username`),
  KEY `group_id` (`group_id`),
  KEY `reg_time` (`reg_time`),
  KEY `uuid` (`uuid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `mip_users`
--

INSERT INTO `mip_users` (`uid`, `uuid`, `username`, `nickname`, `password`, `salt`, `email`, `mobile`, `qq`, `sex`, `group_id`, `rank`, `login_num`, `reg_ip`, `reg_time`, `last_login_ip`, `last_login_time`, `friend_num`, `signature`, `article_num`, `article_comments_num`, `article_views_num`, `status`, `collect`, `terminal`) VALUES
(1, '', 'admin', NULL, '270040f5e07f5a5b0be843222c399aeb', '68fecd', NULL, NULL, '', 1, 1, 1, 0, '0.0.0.0', 0, '0.0.0.0', 0, 0, NULL, 0, 0, 0, 0, 0, 'pc');

-- --------------------------------------------------------

--
-- 表的结构 `mip_users_group`
--

CREATE TABLE IF NOT EXISTS `mip_users_group` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `mip_users_group`
--

INSERT INTO `mip_users_group` (`group_id`, `name`, `icon`, `sort`, `status`, `remark`, `isdelete`, `create_time`, `update_time`) VALUES
(1, '超级管理员', '', 1, 1, '', 0, 0, 0),
(2, '注册会员', '', 2, 1, '', 0, 0, 0),
(3, 'VIP会员', '', 3, 1, '', 0, 0, 0),
(4, '临时分组', '', 4, 1, '', 0, 0, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

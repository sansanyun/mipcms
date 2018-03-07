/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 100110
 Source Host           : localhost
 Source Database       : mipzhanquan

 Target Server Type    : MySQL
 Target Server Version : 100110
 File Encoding         : utf-8

 Date: 12/19/2017 09:32:59 AM
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `mip_ad`;
CREATE TABLE `mip_ad` (
  `id` char(50) CHARACTER SET latin1 DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` longtext,
  `add_time` int(11) DEFAULT NULL,
  `info` varchar(255) DEFAULT NULL,
  KEY `id` (`id`),
  KEY `add_time` (`add_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `mip_domain_sites`;
CREATE TABLE `mip_domain_sites` (
  `id` char(24) NOT NULL,
  `http_type` varchar(255) DEFAULT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `template` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
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
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 更新当前版本
--
UPDATE `mip_settings` SET `val` = '310' WHERE `key` = 'localCurrentVersionNum';
 
--
-- 更新当前版本副本
--
UPDATE `mip_settings` SET `val` = 'v3.1.0' WHERE `key` = 'localCurrentVersion';
SET FOREIGN_KEY_CHECKS = 1;

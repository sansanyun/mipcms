SET NAMES utf8;
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

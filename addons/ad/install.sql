
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

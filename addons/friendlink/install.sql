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

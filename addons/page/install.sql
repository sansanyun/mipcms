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
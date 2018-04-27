ALTER TABLE `mip_articles_category` ADD `status` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `mip_articles_category` ADD `is_page` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `mip_articles_category` ADD `category_url` varchar(255) DEFAULT NULL;
ALTER TABLE `mip_articles_category` ADD `category_page_url` varchar(255) DEFAULT NULL;
ALTER TABLE `mip_articles_category` ADD `detail_url` varchar(255) DEFAULT NULL;
ALTER TABLE `mip_articles_category` ADD `detail_template` varchar(255) DEFAULT NULL;

ALTER TABLE `mip_articles_category` ADD `content` longtext;


--
-- 更新当前版本
--
UPDATE `mip_settings` SET `val` = '350' WHERE `key` = 'localCurrentVersionNum';
 
--
-- 更新当前版本副本
--
UPDATE `mip_settings` SET `val` = 'v3.5.0' WHERE `key` = 'localCurrentVersion';
SET FOREIGN_KEY_CHECKS = 1;

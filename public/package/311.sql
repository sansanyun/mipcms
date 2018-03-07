
ALTER TABLE `mip_domain_sites` ADD `data_id` int(11) DEFAULT NULL;

ALTER TABLE `mip_domain_settings` ADD `siteName` varchar(255) DEFAULT NULL;
ALTER TABLE `mip_domain_settings` ADD `indexTitle` varchar(255) DEFAULT NULL;
ALTER TABLE `mip_domain_settings` ADD `diySiteName` varchar(255) DEFAULT NULL;
ALTER TABLE `mip_domain_settings` ADD `keywords` varchar(255) DEFAULT NULL;
ALTER TABLE `mip_domain_settings` ADD `description` varchar(255) DEFAULT NULL;
ALTER TABLE `mip_domain_settings` ADD `icp` varchar(255) DEFAULT NULL;
ALTER TABLE `mip_domain_settings` ADD `statistical` varchar(255) DEFAULT NULL;

--
-- 更新当前版本
--
UPDATE `mip_settings` SET `val` = '311' WHERE `key` = 'localCurrentVersionNum';
 
--
-- 更新当前版本副本
--
UPDATE `mip_settings` SET `val` = 'v3.1.1' WHERE `key` = 'localCurrentVersion';
SET FOREIGN_KEY_CHECKS = 1;

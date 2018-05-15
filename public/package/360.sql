ALTER TABLE `mip_tags` MODIFY COLUMN description text;  
ALTER TABLE `mip_articles` MODIFY COLUMN description text;  
ALTER TABLE `mip_articles_category` MODIFY COLUMN description text;  
ALTER TABLE `mip_domain_settings` MODIFY COLUMN description text;  
ALTER TABLE `mip_domain_settings` MODIFY COLUMN statistical text;
ALTER TABLE `mip_tags_category` MODIFY COLUMN description text;  
ALTER TABLE `mip_addons` MODIFY COLUMN description text;  

--
-- 更新当前版本
--
UPDATE `mip_settings` SET `val` = '360' WHERE `key` = 'localCurrentVersionNum';
 
--
-- 更新当前版本副本
--
UPDATE `mip_settings` SET `val` = 'v3.6.0' WHERE `key` = 'localCurrentVersion';
SET FOREIGN_KEY_CHECKS = 1;

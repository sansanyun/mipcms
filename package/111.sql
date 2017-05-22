--
-- 在友情链接中增加排序字段
--
ALTER TABLE `mip_friendlink` ADD `sort` INT( 5 ) NULL DEFAULT '99', ADD INDEX ( `sort` ) ;
--
-- 在友情链接中增加类型字段
--
ALTER TABLE `mip_friendlink` ADD `type` varchar( 100 ) NULL DEFAULT 'all' ;

--
-- 在友情链接中增加时间段
--
ALTER TABLE `mip_friendlink` ADD `add_time` int(11) unsigned zerofill NOT NULL, ADD INDEX ( `add_time` ) ;

--
-- 在友情链接中增加状态
--
ALTER TABLE `mip_friendlink` ADD `status` INT( 1 ) NULL DEFAULT '0';



--
-- 权限添加
--

--
-- 添加友情链接权限名称
--
INSERT INTO `mip_roles_node` (`id`, `pid`, `group_id`, `name`, `title`, `remark`, `level`, `type`, `sort`, `status`, `isdelete`) VALUES (53, 2, 0, 'Link', '友情链接', '', 2, 1, 50, 1, 0);
INSERT INTO `mip_roles_node` (`id`, `pid`, `group_id`, `name`, `title`, `remark`, `level`, `type`, `sort`, `status`, `isdelete`) VALUES (54, 53, 0, 'friendlinkSelect', '友情链接查询', '', 3, 1, 50, 1, 0);
INSERT INTO `mip_roles_node` (`id`, `pid`, `group_id`, `name`, `title`, `remark`, `level`, `type`, `sort`, `status`, `isdelete`) VALUES (55, 53, 0, 'friendlinkAdd', '友情链接添加', '', 3, 1, 50, 1, 0);
INSERT INTO `mip_roles_node` (`id`, `pid`, `group_id`, `name`, `title`, `remark`, `level`, `type`, `sort`, `status`, `isdelete`) VALUES (56, 53, 0, 'friendlinkSave', '友情链接排序', '', 3, 1, 50, 1, 0);
INSERT INTO `mip_roles_node` (`id`, `pid`, `group_id`, `name`, `title`, `remark`, `level`, `type`, `sort`, `status`, `isdelete`) VALUES (57, 53, 0, 'friendlinkEdit', '友情链接修改', '', 3, 1, 50, 1, 0);
INSERT INTO `mip_roles_node` (`id`, `pid`, `group_id`, `name`, `title`, `remark`, `level`, `type`, `sort`, `status`, `isdelete`) VALUES (58, 53, 0, 'friendlinkDel', '友情链接删除', '', 3, 1, 50, 1, 0);



--
-- 将友情链接的权限添加到管理员的组里
--
INSERT INTO `mip_roles_access` (`group_id`, `node_id`, `level`, `pid`) VALUES (1, 53, 2, 2);
INSERT INTO `mip_roles_access` (`group_id`, `node_id`, `level`, `pid`) VALUES (1, 54, 3, 53);
INSERT INTO `mip_roles_access` (`group_id`, `node_id`, `level`, `pid`) VALUES (1, 55, 3, 53);
INSERT INTO `mip_roles_access` (`group_id`, `node_id`, `level`, `pid`) VALUES (1, 56, 3, 53);
INSERT INTO `mip_roles_access` (`group_id`, `node_id`, `level`, `pid`) VALUES (1, 57, 3, 53);
INSERT INTO `mip_roles_access` (`group_id`, `node_id`, `level`, `pid`) VALUES (1, 58, 3, 53);


--
-- 设置分隔符
--
INSERT INTO `mip_settings` (`key`, `val`) VALUES ('titleSeparator', '_');

--
-- PC统计代码
--
INSERT INTO `mip_settings` (`key`, `val`) VALUES ('pcStatistical', '');


--
-- 版本更新 每个版本都有
--

--
-- 更新当前版本 
--
UPDATE `mip_settings` SET `val` = '111' WHERE `key` = 'localCurrentVersionNum';

--
-- 更新当前版本副本
--
UPDATE `mip_settings` SET `val` = 'v1.1.1' WHERE `key` = 'localCurrentVersion';

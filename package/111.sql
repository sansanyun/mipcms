--
-- 在友情链接中增加排序字段
--
ALTER TABLE `mip_friendlink` ADD `sort` INT( 5 ) NULL DEFAULT '0', ADD INDEX ( `sort` ) ;
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


--
-- 将友情链接的权限添加到管理员的组里
--
INSERT INTO `mip_roles_access` (`group_id`, `node_id`, `level`, `pid`) VALUES (1, 53, 2, 2);
INSERT INTO `mip_roles_access` (`group_id`, `node_id`, `level`, `pid`) VALUES (1, 54, 3, 53);
INSERT INTO `mip_roles_access` (`group_id`, `node_id`, `level`, `pid`) VALUES (1, 55, 3, 53);
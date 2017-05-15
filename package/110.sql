--
-- 添加文章推荐权限名称
--
INSERT INTO `mip_roles_node` (`id`, `pid`, `group_id`, `name`, `title`, `remark`, `level`, `type`, `sort`, `status`, `isdelete`) VALUES (52, 21, 0, 'articleRecomment', '文章推荐', '', 3, 1, 50, 1, 0);

--
-- 将文章推荐的权限添加到管理员的组里
--

INSERT INTO `mip_roles_access` (`group_id`, `node_id`, `level`, `pid`) VALUES (1, 52, 3, 21);


--
-- 版本更新 每个版本都有
--
--
-- 更新当前版本 
--
UPDATE mip_settings SET val = '110' WHERE key = 'localCurrentVersionNum';

--
-- 更新当前版本副本
--
UPDATE mip_settings SET val = 'v1.1.0' WHERE key = 'localCurrentVersion';

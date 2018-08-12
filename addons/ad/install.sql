
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


INSERT INTO `mip_ad` VALUES ('45962c12f4b7b0d1e907c017', 'indexSideA', '首页-侧边栏A', '&lt;div class=&quot;mip-box&quot;&gt;\n    &lt;div class=&quot;mip-box-heading&quot;&gt;\n        &lt;h3 class=&quot;title&quot;&gt;加入QQ群聊&lt;/h3&gt;\n    &lt;/div&gt;\n    &lt;div class=&quot;mip-box-body&quot;&gt;\n&lt;a href=&quot;https://jq.qq.com/?_wv=1027&amp;amp;k=50nc1KR&quot;&gt;【MIPCMS官方群】&lt;/a&gt;\n    &lt;/div&gt;\n&lt;/div&gt;', '1534038063', '');
INSERT INTO `mip_ad` VALUES ('a35ef2093ddcfd765a79e519', 'indexSideB', '首页-侧边栏B', '&lt;div class=&quot;mip-box&quot;&gt;\n    &lt;div class=&quot;mip-box-heading&quot;&gt;\n        &lt;h3 class=&quot;title&quot;&gt;站群广告&lt;/h3&gt;\n    &lt;/div&gt;\n    &lt;div class=&quot;mip-box-body&quot;&gt;\n&lt;a href=&quot;https://www.mipcms.cn/article/7206ddd34b462a0c192e10c8.html&quot;&gt;&lt;mip-img src=\'http://img.mipmb.com/260x200/bgColor-3b5c61__color-ffffff__text-MIP站群免费体验\'&gt;&lt;/mip-img&gt;\n&lt;/a&gt;\n    &lt;/div&gt;\n&lt;/div&gt;', '1534038227', '');
INSERT INTO `mip_ad` VALUES ('e10698e66e7dfa652bc2eb2e', 'categoryListTop', '分类-列表顶部', '&lt;div class=\'mb-3\'&gt;\n&lt;a href=&quot;https://www.mipcms.cn/article/7206ddd34b462a0c192e10c8.html&quot;&gt;&lt;mip-img width=\'728\' height=\'100\' \n src=\'http://img.mipmb.com/728x100/bgColor-3730be__color-ffffff__text-基于百度移动MIP打造的站群管理系统\'&gt;&lt;/mip-img&gt;\n&lt;/a&gt;\n&lt;/div&gt;', '1534038346', '');
INSERT INTO `mip_ad` VALUES ('d75f083a1ea16c47a07707d0', 'categorySideA', '分类-侧边栏A', '&lt;div class=&quot;mip-box&quot;&gt;\n    &lt;div class=&quot;mip-box-heading&quot;&gt;\n        &lt;h3 class=&quot;title&quot;&gt;站群广告&lt;/h3&gt;\n    &lt;/div&gt;\n    &lt;div class=&quot;mip-box-body&quot;&gt;\n&lt;a href=&quot;https://www.mipcms.cn/article/7206ddd34b462a0c192e10c8.html&quot;&gt;&lt;mip-img  width=\'260\' height=\'200\' \n src=\'http://img.mipmb.com/260x200/bgColor-fff4e8__color-cb8660__text-MIP站群免费体验\'&gt;&lt;/mip-img&gt;\n&lt;/a&gt;\n    &lt;/div&gt;\n&lt;/div&gt;', '1534038380', '');
INSERT INTO `mip_ad` VALUES ('af71f408a09ece34edd7d5d4', 'categorySideB', '分类-侧边栏B', '&lt;a href=&quot;https://www.mipcms.cn/article/7206ddd34b462a0c192e10c8.html&quot;&gt;&lt;mip-img  width=\'300\' height=\'200\' \n src=\'http://img.mipmb.com/300x200/bgColor-baeaff__color-42bbda__text-MIP站群免费下载\'&gt;&lt;/mip-img&gt;\n&lt;/a&gt;', '1534038414', '');
INSERT INTO `mip_ad` VALUES ('cab6914a76da3c98d84dfdc9', 'detailContentTop', '详情-内容顶部', '&lt;div class=\'mb-3\'&gt;\n&lt;a href=&quot;https://www.mipcms.cn/article/7206ddd34b462a0c192e10c8.html&quot;&gt;&lt;mip-img width=\'728\' height=\'100\' src=\'http://img.mipmb.com/728x100/bgColor-3730be__color-ffffff__text-基于百度移动MIP打造的站群管理系统\'&gt;&lt;/mip-img&gt;\n&lt;/a&gt;\n&lt;/div&gt;', '1534038464', '');
INSERT INTO `mip_ad` VALUES ('8a635cfa6df7597118479550', 'detailContentBottom', '详情-内容底部', '&lt;div class=\'mb-3\'&gt;\n&lt;a href=&quot;https://www.mipcms.cn/article/7206ddd34b462a0c192e10c8.html&quot;&gt;&lt;mip-img width=\'728\' height=\'100\' \n src=\'http://img.mipmb.com/728x100/bgColor-3730be__color-ffffff__text-基于百度移动MIP打造的站群管理系统\'&gt;&lt;/mip-img&gt;\n&lt;/a&gt;\n&lt;/div&gt;', '1534038497', '');
INSERT INTO `mip_ad` VALUES ('dff00693221ad8c2c466f863', 'detailSideA', '详情-侧边栏A', '&lt;div class=&quot;mip-box&quot;&gt;\n    &lt;div class=&quot;mip-box-heading&quot;&gt;\n        &lt;h3 class=&quot;title&quot;&gt;站群广告&lt;/h3&gt;\n    &lt;/div&gt;\n    &lt;div class=&quot;mip-box-body&quot;&gt;\n&lt;a href=&quot;https://www.mipcms.cn/article/7206ddd34b462a0c192e10c8.html&quot;&gt;&lt;mip-img width=\'260\' height=\'200\' \n src=\'http://img.mipmb.com/260x200/bgColor-fff4e8__color-cb8660__text-MIP站群免费体验\'&gt;&lt;/mip-img&gt;\n&lt;/a&gt;\n    &lt;/div&gt;\n&lt;/div&gt;', '1534038545', '');
INSERT INTO `mip_ad` VALUES ('3184310eb271d084f60415f9', 'detailSideB', '详情-侧边栏B', '&lt;div class=\'mb-3\'&gt;&lt;a href=&quot;https://www.mipcms.cn/article/7206ddd34b462a0c192e10c8.html&quot;&gt;&lt;mip-img  \nwidth=\'300\' height=\'200\' src=\'http://img.mipmb.com/300x200/bgColor-baeaff__color-42bbda__text-MIP站群免费下载\'&gt;&lt;/mip-img&gt;\n&lt;/a&gt;&lt;/div&gt;', '1534038579', '');

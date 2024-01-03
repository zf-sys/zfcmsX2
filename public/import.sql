/*
Navicat MySQL Data Transfer

Source Server         : plugin_x2
Source Server Version : 50650
Source Host           : 47.106.94.153:3306
Source Database       : plugin_x2_zfcms_

Target Server Type    : MYSQL
Target Server Version : 50650
File Encoding         : 65001

Date: 2022-08-07 08:18:44
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for zf_admin
-- ----------------------------
DROP TABLE IF EXISTS `zf_admin`;
CREATE TABLE `zf_admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `gid` int(11) DEFAULT NULL COMMENT '管理员分类id',
  `name` varchar(25) NOT NULL,
  `pwd` varchar(250) NOT NULL,
  `tel` varchar(11) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `age` tinyint(3) DEFAULT NULL,
  `sex` tinyint(1) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `admin_group_id` tinyint(2) NOT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `login_num` int(11) NOT NULL DEFAULT '0',
  `ctime` int(11) DEFAULT NULL,
  `sort` tinyint(5) NOT NULL DEFAULT '1',
  `google_secret` varchar(255) DEFAULT NULL,
  `google_is` tinyint(255) NOT NULL DEFAULT '0',
  `pic` varchar(255) NOT NULL DEFAULT 'https://i.loli.net/2019/10/29/9OCU2VXHtAFhzoT.jpg',
  `token` varchar(255) NOT NULL DEFAULT '',
  `wxlogin_is` tinyint(1) NOT NULL,
  `wxlogin_id` varchar(255) NOT NULL,
  `wxlogin_token` varchar(255) NOT NULL,
  `lang` varchar(50) NOT NULL DEFAULT '',
  `lang_pid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='管理员表';

-- ----------------------------
-- Records of zf_admin
-- ----------------------------
INSERT INTO `zf_admin` VALUES ('1', '1', 'admin', '', '', '', '0', '1', '23', '1', '0', '', '0', null, '0', '76DGRK3PKWYNP23Z', '0', 'http://oss002.wangmingchang.com/uploads/397936734957a99a5aff68e9c62e62fd/20210818_772390_c53495d1c7fc87064d38912ba538f887.jpg', '1646748339', '1', 'oPMmW5v11jyHKr5PA7IanuzavkY8', 'b1393f98b99333301f6cc92c8fbeed12', '', '0');

-- ----------------------------
-- Table structure for zf_admin_group
-- ----------------------------
DROP TABLE IF EXISTS `zf_admin_group`;
CREATE TABLE `zf_admin_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `ctime` int(11) DEFAULT NULL,
  `sort` tinyint(5) NOT NULL DEFAULT '1',
  `role` varchar(1000) NOT NULL DEFAULT '0',
  `token` varchar(255) NOT NULL DEFAULT '',
  `lang` varchar(50) NOT NULL DEFAULT '',
  `lang_pid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='管理员分组表';

-- ----------------------------
-- Records of zf_admin_group
-- ----------------------------
INSERT INTO `zf_admin_group` VALUES ('1', '超级管理员', '1', null, '1', '180,182,183,184,185,193,197,210,211,212,213,186,187,188,170,171,172,173,174,175,176,177,178,179,154,157,163,164,167,158,161,162,159,165,166,168,160,169,201,202,203,194,195,196,189,190,191,192,204,205,198,199,200,209,206,207,208', '', '', '0');
INSERT INTO `zf_admin_group` VALUES ('2', '普通管理员', '1', null, '1', '181,180,182,183,184,185,186,187,188,170,171,172,173,174,175,176,177,178,179,189,190,191,192,154,157,163,164,167,158,161,162,159,165,166,168,160,169', '', '', '0');

-- ----------------------------
-- Table structure for zf_admin_log
-- ----------------------------
DROP TABLE IF EXISTS `zf_admin_log`;
CREATE TABLE `zf_admin_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `action` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
  `ctime` int(11) NOT NULL,
  `ip` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
  `uid` int(11) NOT NULL,
  `post` text CHARACTER SET utf8mb4 NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `token` varchar(255) NOT NULL DEFAULT '',
  `lang` varchar(50) NOT NULL DEFAULT '',
  `lang_pid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='后台访问日志表';

-- ----------------------------
-- Table structure for zf_admin_role
-- ----------------------------
DROP TABLE IF EXISTS `zf_admin_role`;
CREATE TABLE `zf_admin_role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `value` varchar(50) DEFAULT NULL,
  `check` tinyint(1) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `summary` varchar(255) DEFAULT NULL,
  `sort` tinyint(10) NOT NULL DEFAULT '0',
  `pid` int(11) NOT NULL DEFAULT '0',
  `module` varchar(255) DEFAULT NULL,
  `control` varchar(50) NOT NULL DEFAULT '',
  `act` varchar(50) NOT NULL DEFAULT '',
  `menu` tinyint(1) NOT NULL DEFAULT '1',
  `parm` varchar(255) DEFAULT NULL,
  `token` varchar(255) NOT NULL DEFAULT '',
  `icon` varchar(255) DEFAULT NULL,
  `lang` varchar(50) NOT NULL DEFAULT '',
  `lang_pid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员权限表';

-- ----------------------------
-- Records of zf_admin_role
-- ----------------------------
INSERT INTO `zf_admin_role` (`id`, `name`, `value`, `check`, `status`, `summary`, `sort`, `pid`, `module`, `control`, `act`, `menu`, `parm`, `token`, `icon`, `lang`, `lang_pid`) VALUES
(154,	'网站设置',	'admin/Config/',	1,	1,	NULL,	10,	218,	'admin',	'Config',	'',	1,	'',	'1680477269',	'fa fa-cog',	'',	0),
(157,	'管理组',	'admin/Config/admin_group',	1,	1,	NULL,	0,	219,	'admin',	'Config',	'admin_group',	1,	'',	'1680477401',	'fa fa-users',	'',	0),
(158,	'管理员列表',	'admin/Config/admin_index',	1,	1,	NULL,	0,	219,	'admin',	'Config',	'admin_index',	1,	'',	'1680477410',	'fa fa-user-secret',	'',	0),
(159,	'权限管理',	'admin/Config/admin_role',	1,	1,	NULL,	0,	219,	'admin',	'Config',	'admin_role',	1,	'',	'1680477424',	'fa fa-american-sign-language-interpreting',	'',	0),
(160,	'操作日志',	'admin/Config/action_log',	1,	1,	NULL,	4,	154,	'admin',	'Config',	'action_log',	1,	'',	'1638409211',	'fa fa-bandcamp',	'',	0),
(161,	'新增管理员',	'admin/Config/admin_add',	1,	1,	NULL,	0,	158,	'admin',	'Config',	'admin_add',	0,	'',	'',	'',	'',	0),
(162,	'编辑管理员',	'admin/Config/admin_edit',	1,	1,	NULL,	0,	158,	'admin',	'Config',	'admin_edit',	0,	'',	'',	'',	'',	0),
(163,	'新增管理组',	'admin/Config/admin_group_add',	1,	1,	NULL,	0,	157,	'admin',	'Config',	'admin_group_add',	0,	'',	'',	NULL,	'',	0),
(164,	'编辑管理组',	'admin/Config/admin_group_edit',	1,	1,	NULL,	0,	157,	'admin',	'Config',	'admin_group_edit',	0,	'',	'',	NULL,	'',	0),
(165,	'新增权限',	'admin/Config/admin_role_add',	1,	1,	NULL,	0,	159,	'admin',	'Config',	'admin_role_add',	0,	'',	'',	NULL,	'',	0),
(166,	'编辑权限',	'admin/Config/admin_role_edit',	1,	1,	NULL,	0,	159,	'admin',	'Config',	'admin_role_edit',	0,	'',	'',	NULL,	'',	0),
(167,	'管理组权限',	'admin/Config/admin_group_role',	1,	1,	NULL,	0,	157,	'admin',	'Config',	'admin_group_role',	0,	'',	'',	NULL,	'',	0),
(168,	'获取方法',	'admin/Config/get_action',	1,	1,	NULL,	0,	159,	'admin',	'Config',	'get_action',	0,	'',	'',	NULL,	'',	0),
(169,	'OSS设置',	'admin/Config/oss_config',	1,	1,	NULL,	3,	154,	'admin',	'Config',	'oss_config',	1,	'',	'1638409235',	'fa fa-arrow-circle-up',	'',	0),
(170,	'其他设置',	'admin/Rests/',	1,	1,	NULL,	30,	180,	'admin',	'Rests',	'',	1,	'',	'1638408588',	'fa fa-cubes',	'',	0),
(171,	'广告管理',	'admin/Rests/advert',	1,	1,	NULL,	0,	170,	'admin',	'Rests',	'advert',	1,	'',	'1634719759',	'fa fa-adjust',	'',	0),
(172,	'新增广告',	'admin/Rests/advert_add',	1,	1,	NULL,	0,	171,	'admin',	'Rests',	'advert_add',	0,	'',	'',	NULL,	'',	0),
(173,	'编辑广告',	'admin/Rests/advert_edit',	1,	1,	NULL,	0,	171,	'admin',	'Rests',	'advert_edit',	0,	'',	'',	NULL,	'',	0),
(174,	'超链管理',	'admin/Rests/link',	1,	1,	NULL,	0,	170,	'admin',	'Rests',	'link',	1,	'',	'1634719773',	'fa fa-link',	'',	0),
(175,	'新增超链',	'admin/Rests/link_add',	1,	1,	NULL,	0,	174,	'admin',	'Rests',	'link_add',	0,	'',	'',	NULL,	'',	0),
(176,	'编辑超链',	'admin/Rests/link_edit',	1,	1,	NULL,	0,	174,	'admin',	'Rests',	'link_edit',	0,	'',	'',	NULL,	'',	0),
(177,	'留言管理',	'admin/Rests/guessbook',	1,	1,	NULL,	0,	170,	'admin',	'Rests',	'guessbook',	1,	'',	'1634719799',	'fa fa-align-justify',	'',	0),
(178,	'新增留言',	'admin/Rests/guessbook_add',	1,	1,	NULL,	0,	177,	'admin',	'Rests',	'guessbook_add',	0,	'',	'',	NULL,	'',	0),
(179,	'留言详情',	'admin/Rests/guessbook_edit',	1,	1,	NULL,	0,	177,	'admin',	'Rests',	'guessbook_edit',	0,	'',	'',	NULL,	'',	0),
(180,	'内容',	'admin/Category/',	1,	1,	NULL,	20,	0,	'admin',	'Category',	'',	1,	'',	'1680477523',	'fa fa-book',	'',	0),
(182,	'内容板块',	'admin/Category/index',	1,	1,	NULL,	0,	180,	'admin',	'Category',	'index',	1,	'',	'1646270646',	'fa fa-book',	'',	0),
(183,	'内容模型',	'admin/Category/category_model',	1,	1,	NULL,	0,	180,	'admin',	'Category',	'category_model',	1,	'',	'',	'fa fa-modx',	'',	0),
(184,	'内容列表',	'admin/Category/post_all_list',	1,	1,	NULL,	0,	180,	'admin',	'Category',	'post_all_list',	1,	'',	'',	'fa fa-book',	'',	0),
(185,	'专题列表',	'admin/Category/special',	1,	1,	NULL,	0,	180,	'admin',	'Category',	'special',	1,	'',	'1634719563',	'fa fa-book',	'',	0),
(186,	'用户管理',	'admin/User/',	1,	1,	NULL,	20,	220,	'admin',	'User',	'',	1,	'',	'1680478112',	'fa fa-user-circle',	'',	0),
(187,	'用户列表',	'admin/User/index',	1,	1,	NULL,	0,	186,	'admin',	'User',	'index',	1,	'',	'1634719634',	'fa fa-user',	'',	0),
(188,	'用户分组',	'admin/User/group',	1,	1,	NULL,	0,	186,	'admin',	'User',	'group',	1,	'',	'1634719658',	'fa fa-users',	'',	0),
(193,	'Tag标签',	'admin/Category/tag',	1,	1,	NULL,	0,	180,	'admin',	'Category',	'tag',	1,	'',	'1634719581',	'fa fa-tags',	'',	0),
(195,	'分组',	'plugins/zf_querylist.index/cate',	1,	1,	NULL,	0,	194,	'plugins',	'index',	'cate',	1,	'',	'',	'',	'',	0),
(196,	'列表',	'plugins/zf_querylist.index/index',	1,	1,	NULL,	0,	194,	'plugins',	'index',	'index',	1,	'',	'',	'',	'',	0),
(198,	'商店',	'admin/0/0',	1,	1,	NULL,	80,	0,	'admin',	'0',	'0',	1,	'',	'1637805157',	'',	'',	0),
(199,	'模板',	'admin/Zfyun/themes',	1,	1,	NULL,	1,	198,	'admin',	'Zfyun',	'themes',	1,	'',	'1634719930',	'fa fa-align-justify',	'',	0),
(200,	'插件',	'admin/Zfyun/plugins',	1,	1,	NULL,	2,	198,	'admin',	'Zfyun',	'plugins',	1,	'',	'1634719907',	'fa fa-plug',	'',	0),
(201,	'其他参数',	'admin/Config/custom_config',	1,	1,	NULL,	2,	154,	'admin',	'Config',	'custom_config',	1,	'',	'1638409253',	'fa fa-sticky-note-o',	'',	0),
(202,	'基本设置',	'admin/Config/index',	1,	1,	NULL,	0,	154,	'admin',	'Config',	'index',	1,	'',	'1638409341',	'fa fa-cube',	'',	0),
(203,	'邮箱配置',	'admin/Config/email',	1,	1,	NULL,	1,	154,	'admin',	'Config',	'email',	1,	'',	'1638409286',	'fa fa-window-maximize',	'',	0),
(209,	'升级',	'admin/Zfyun/upgrade',	1,	1,	NULL,	5,	198,	'admin',	'Zfyun',	'upgrade',	1,	'',	'1637822193',	'fa fa-gratipay',	'',	0),
(213,	'菜单管理',	'admin/Rests/menu',	1,	1,	NULL,	0,	170,	'admin',	'Rests',	'menu',	1,	'',	'1644927259',	'fa fa-certificate',	'',	0),
(214,	'论坛板块',	'admin/Category/index',	1,	1,	NULL,	0,	180,	'admin',	'Category',	'index',	1,	'type=bbs',	'1646224208',	'fa fa-500px',	'',	0),
(215,	'在线商店',	'admin/Zfyun/store',	1,	1,	NULL,	6,	198,	'admin',	'Zfyun',	'store',	1,	'',	'1646401835',	'fa fa-window-restore',	'',	0),
(216,	'钩子',	'admin/Zfyun/hook',	1,	1,	NULL,	3,	198,	'admin',	'Zfyun',	'hook',	1,	'',	'1648289288',	'fa fa-500px',	'',	0),
(217,	'任务',	'admin/Zfyun/task',	1,	1,	NULL,	4,	198,	'admin',	'Zfyun',	'task',	1,	'',	'1656988690',	'fa fa-bolt',	'',	0),
(218,	'系统',	'admin/0/0',	1,	1,	NULL,	10,	0,	'admin',	'0',	'0',	1,	'',	'1680477301',	'fa fa-server',	'',	0),
(219,	'权限管理',	'admin/0/0',	1,	1,	NULL,	20,	218,	'admin',	'0',	'0',	1,	'',	'1680482183',	'fa fa-rocket',	'',	0),
(220,	'会员',	'admin/0/0',	1,	1,	NULL,	30,	0,	'admin',	'0',	'0',	1,	'',	'',	NULL,	'',	0),
(221,	'系统参数',	'admin/0/0',	1,	1,	NULL,	30,	218,	'admin',	'0',	'0',	1,	'',	'1680499509',	'fa fa-adn',	'',	0),
(222,	'版本信息',	'admin/Config/version',	1,	1,	NULL,	0,	221,	'admin',	'Config',	'version',	1,	'',	'',	NULL,	'',	0),
(223,	'授权信息',	'admin/Config/zf_auth',	1,	1,	NULL,	0,	221,	'admin',	'Config',	'zf_auth',	1,	'',	'',	NULL,	'',	0),
(224,	'测试邮件',	'admin/Config/test_email',	1,	1,	NULL,	0,	203,	'admin',	'Config',	'test_email',	0,	'',	'',	NULL,	'',	0),
(225,	'其他参数修改',	'admin/Config/custom_config_edit',	1,	1,	NULL,	0,	201,	'admin',	'Config',	'custom_config_edit',	0,	'',	'',	NULL,	'',	0),
(226,	'批量修改栏目',	'admin/Category/cate_all_edit',	1,	1,	NULL,	0,	182,	'admin',	'Category',	'cate_all_edit',	0,	'',	'',	NULL,	'',	0),
(227,	'新增栏目',	'admin/Category/category_add',	1,	1,	NULL,	0,	182,	'admin',	'Category',	'category_add',	0,	'',	'',	NULL,	'',	0),
(228,	'编辑栏目',	'admin/Category/category_edit',	1,	1,	NULL,	0,	182,	'admin',	'Category',	'category_edit',	0,	'',	'',	NULL,	'',	0),
(229,	'新增模型',	'admin/Category/category_model_add',	1,	1,	NULL,	0,	183,	'admin',	'Category',	'category_model_add',	0,	'',	'',	NULL,	'',	0),
(230,	'编辑模型',	'admin/Category/category_model_edit',	1,	1,	NULL,	0,	183,	'admin',	'Category',	'category_model_edit',	0,	'',	'',	NULL,	'',	0),
(231,	'文章列表',	'admin/Category/post_list',	1,	1,	NULL,	0,	184,	'admin',	'Category',	'post_list',	0,	'',	'1680490759',	'',	'',	0),
(232,	'批量新增内容',	'admin/Category/post_add_pl',	1,	1,	NULL,	0,	184,	'admin',	'Category',	'post_add_pl',	0,	'',	'',	NULL,	'',	0),
(233,	'新增/编辑内容',	'admin/Category/post_add',	1,	1,	NULL,	0,	184,	'admin',	'Category',	'post_add',	0,	'',	'',	NULL,	'',	0),
(234,	'导入内容',	'admin/Category/import',	1,	1,	NULL,	0,	184,	'admin',	'Category',	'import',	0,	'',	'',	NULL,	'',	0),
(235,	'根据关键字搜索内容',	'admin/Category/ajax_search_post',	1,	1,	NULL,	0,	184,	'admin',	'Category',	'ajax_search_post',	0,	'',	'',	NULL,	'',	0),
(236,	'模型参数列表',	'admin/Category/category_model_parm',	1,	1,	NULL,	0,	183,	'admin',	'Category',	'category_model_parm',	0,	'',	'',	NULL,	'',	0),
(237,	'模型参数列表新增',	'admin/Category/category_model_parm_add',	1,	1,	NULL,	0,	236,	'admin',	'Category',	'category_model_parm_add',	0,	'',	'',	NULL,	'',	0),
(238,	'模型参数列表编辑',	'admin/Category/category_model_parm_edit',	1,	1,	NULL,	0,	183,	'admin',	'Category',	'category_model_parm_edit',	0,	'',	'',	NULL,	'',	0),
(239,	'新增专题',	'admin/Category/special_add',	1,	1,	NULL,	0,	185,	'admin',	'Category',	'special_add',	0,	'',	'',	NULL,	'',	0),
(240,	'编辑专题',	'admin/Category/special_edit',	1,	1,	NULL,	0,	185,	'admin',	'Category',	'special_edit',	0,	'',	'',	NULL,	'',	0),
(241,	'专题关联',	'admin/Category/special_post_edit',	1,	1,	NULL,	0,	185,	'admin',	'Category',	'special_post_edit',	0,	'',	'',	NULL,	'',	0),
(242,	'新增标签',	'admin/Category/tag_add',	1,	1,	NULL,	0,	193,	'admin',	'Category',	'tag_add',	0,	'',	'',	NULL,	'',	0),
(243,	'编辑标签',	'admin/Category/tag_edit',	1,	1,	NULL,	0,	193,	'admin',	'Category',	'tag_edit',	0,	'',	'',	NULL,	'',	0),
(244,	'Index',	'admin/Index/',	1,	1,	NULL,	0,	0,	'admin',	'Index',	'',	0,	'',	'',	NULL,	'',	0),
(245,	'欢迎页',	'admin/Index/welcome',	1,	1,	NULL,	0,	244,	'admin',	'Index',	'welcome',	0,	'',	'',	NULL,	'',	0),
(246,	'清除伪删除数据',	'admin/Index/db_clear',	1,	1,	NULL,	0,	244,	'admin',	'Index',	'db_clear',	0,	'',	'',	NULL,	'',	0),
(247,	'清除缓存',	'admin/Index/temp_clear',	1,	1,	NULL,	0,	244,	'admin',	'Index',	'temp_clear',	0,	'',	'',	NULL,	'',	0),
(248,	'切换语言',	'admin/Index/change_lang',	1,	1,	NULL,	0,	244,	'admin',	'Index',	'change_lang',	0,	'',	'',	NULL,	'',	0),
(249,	'公共',	'admin/Common/',	1,	1,	NULL,	0,	0,	'admin',	'Common',	'',	0,	'',	'',	NULL,	'',	0),
(250,	'验证码',	'admin/Common/verify',	1,	1,	NULL,	0,	249,	'admin',	'Common',	'verify',	0,	'',	'',	NULL,	'',	0),
(251,	'状态切换',	'admin/Common/is_switch',	1,	1,	NULL,	0,	249,	'admin',	'Common',	'is_switch',	0,	'',	'',	NULL,	'',	0),
(252,	'是否推荐',	'admin/Common/is_recommend',	1,	1,	NULL,	0,	249,	'admin',	'Common',	'is_recommend',	0,	'',	'',	NULL,	'',	0),
(253,	'是否置顶',	'admin/Common/is_istop',	1,	1,	NULL,	0,	249,	'admin',	'Common',	'is_istop',	0,	'',	'',	NULL,	'',	0),
(254,	'是否菜单',	'admin/Common/is_menu',	1,	1,	NULL,	0,	249,	'admin',	'Common',	'is_menu',	0,	'',	'',	NULL,	'',	0),
(255,	'删除内容',	'admin/Common/del_post',	1,	1,	NULL,	0,	249,	'admin',	'Common',	'del_post',	0,	'',	'',	NULL,	'',	0),
(256,	'批量删除',	'admin/Common/more_del',	1,	1,	NULL,	0,	249,	'admin',	'Common',	'more_del',	0,	'',	'',	NULL,	'',	0),
(257,	'修改字段',	'admin/Common/value_edit',	1,	1,	NULL,	0,	249,	'admin',	'Common',	'value_edit',	0,	'',	'1680492510',	'',	'',	0),
(258,	'配置修改',	'admin/Common/config_edit',	1,	1,	NULL,	0,	249,	'admin',	'Common',	'config_edit',	0,	'',	'',	NULL,	'',	0),
(259,	'新增菜单',	'admin/Rests/menu_add',	1,	1,	NULL,	0,	213,	'admin',	'Rests',	'menu_add',	0,	'',	'',	NULL,	'',	0),
(260,	'编辑菜单',	'admin/Rests/menu_edit',	1,	1,	NULL,	0,	213,	'admin',	'Rests',	'menu_edit',	0,	'',	'',	NULL,	'',	0),
(261,	'新增用户',	'admin/User/add',	1,	1,	NULL,	0,	187,	'admin',	'User',	'add',	0,	'',	'',	NULL,	'',	0),
(262,	'编辑用户',	'admin/User/edit',	1,	1,	NULL,	0,	187,	'admin',	'User',	'edit',	0,	'',	'',	NULL,	'',	0),
(263,	'新增用户组',	'admin/User/group_add',	1,	1,	NULL,	0,	188,	'admin',	'User',	'group_add',	0,	'',	'',	NULL,	'',	0),
(264,	'编辑用户组',	'admin/User/group_edit',	1,	1,	NULL,	0,	188,	'admin',	'User',	'group_edit',	0,	'',	'',	NULL,	'',	0),
(265,	'修改密码',	'admin/User/pwd_edit',	1,	1,	NULL,	0,	244,	'admin',	'User',	'pwd_edit',	0,	'',	'',	NULL,	'',	0),
(266,	'账号信息',	'admin/User/admin_info',	1,	1,	NULL,	0,	244,	'admin',	'User',	'admin_info',	0,	'',	'',	NULL,	'',	0),
(267,	'导出用户',	'admin/User/export',	1,	1,	NULL,	0,	187,	'admin',	'User',	'export',	0,	'',	'1680493511',	'',	'',	0),
(268,	'模板上传',	'admin/Zfyun/themes_upload',	1,	1,	NULL,	0,	199,	'admin',	'Zfyun',	'themes_upload',	0,	'',	'',	NULL,	'',	0),
(269,	'插件上传',	'admin/Zfyun/plugin_upload',	1,	1,	NULL,	0,	200,	'admin',	'Zfyun',	'plugin_upload',	0,	'',	'',	NULL,	'',	0),
(270,	'插件卸载',	'admin/Zfyun/plugin_uninstall',	1,	1,	NULL,	0,	200,	'admin',	'Zfyun',	'plugin_uninstall',	0,	'',	'',	NULL,	'',	0),
(271,	'模板下载',	'admin/Zfyun/themes_uninstall',	1,	1,	NULL,	0,	199,	'admin',	'Zfyun',	'themes_uninstall',	0,	'',	'',	NULL,	'',	0),
(272,	'插件备份',	'admin/Zfyun/plugin_backup',	1,	1,	NULL,	0,	200,	'admin',	'Zfyun',	'plugin_backup',	0,	'',	'',	NULL,	'',	0),
(273,	'模板备份',	'admin/Zfyun/theme_backup',	1,	1,	NULL,	0,	199,	'admin',	'Zfyun',	'theme_backup',	0,	'',	'',	NULL,	'',	0),
(274,	'任务日志',	'admin/Zfyun/task_log',	1,	1,	NULL,	0,	217,	'admin',	'Zfyun',	'task_log',	0,	'',	'',	NULL,	'',	0),
(275,	'新增钩子',	'admin/Zfyun/hook_add',	1,	1,	NULL,	0,	216,	'admin',	'Zfyun',	'hook_add',	0,	'',	'',	NULL,	'',	0),
(276,	'编辑钩子',	'admin/Zfyun/hook_edit',	1,	1,	NULL,	0,	216,	'admin',	'Zfyun',	'hook_edit',	0,	'',	'1680494371',	'',	'',	0),
(277,	'新增任务',	'admin/Zfyun/task_add',	1,	1,	NULL,	0,	217,	'admin',	'Zfyun',	'task_add',	0,	'',	'',	NULL,	'',	0),
(278,	'编辑任务',	'admin/Zfyun/task_edit',	1,	1,	NULL,	0,	217,	'admin',	'Zfyun',	'task_edit',	0,	'',	'',	NULL,	'',	0),
(279,	'更新授权',	'admin/Zfyun/update_sq',	1,	1,	NULL,	0,	200,	'admin',	'Zfyun',	'update_sq',	0,	'',	'',	NULL,	'',	0),
(280,	'系统Sql升级',	'admin/Zfyun/upgrade_sql',	1,	1,	NULL,	0,	209,	'admin',	'Zfyun',	'upgrade_sql',	0,	'',	'',	NULL,	'',	0),
(281,	'更新站点授权',	'admin/Zfyun/authentication_sys',	1,	1,	NULL,	0,	209,	'admin',	'Zfyun',	'authentication_sys',	0,	'',	'',	NULL,	'',	0),
(282,	'跳转模板配置',	'admin/Zfyun/jump_theme_config',	1,	1,	NULL,	0,	199,	'admin',	'Zfyun',	'jump_theme_config',	0,	'',	'1680499167',	'',	'',	0),
(283,	'插件操作',	'admin/Zfyun/plugin_act',	1,	1,	NULL,	0,	200,	'admin',	'Zfyun',	'plugin_act',	0,	'',	'',	NULL,	'',	0);

-- ----------------------------
-- Table structure for zf_advert
-- ----------------------------
DROP TABLE IF EXISTS `zf_advert`;
CREATE TABLE `zf_advert` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `pid` int(11) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  `summary` text NOT NULL,
  `content` varchar(255) DEFAULT NULL,
  `target` varchar(255) DEFAULT NULL,
  `cname` varchar(255) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `pic` varchar(255) DEFAULT NULL,
  `sort` int(3) DEFAULT NULL,
  `tag` varchar(25) DEFAULT NULL,
  `end_time` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `token` varchar(255) NOT NULL DEFAULT '',
  `alt_pic` varchar(255) NOT NULL,
  `pic_wap` varchar(255) DEFAULT NULL,
  `lang` varchar(50) NOT NULL DEFAULT '',
  `lang_pid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='广告表';

-- ----------------------------
-- Records of zf_advert
-- ----------------------------
-- ----------------------------
-- Table structure for zf_category
-- ----------------------------
DROP TABLE IF EXISTS `zf_category`;
CREATE TABLE `zf_category` (
  `cid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '父类cid',
  `mid` tinyint(1) NOT NULL DEFAULT '1' COMMENT '模型id',
  `name` varchar(30) NOT NULL COMMENT '标题',
  `ename` varchar(30) DEFAULT NULL COMMENT '英文标题',
  `cname` varchar(255) DEFAULT NULL COMMENT '中文标题',
  `summary` varchar(255) DEFAULT NULL COMMENT '简介',
  `pic` varchar(255) DEFAULT NULL COMMENT '图片',
  `icon` varchar(255) DEFAULT NULL COMMENT '图标',
  `url` varchar(150) DEFAULT NULL COMMENT '外链',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `sort` tinyint(4) NOT NULL DEFAULT '0' COMMENT '排序',
  `menu` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否菜单',
  `ctime` int(11) DEFAULT NULL COMMENT '创建时间',
  `utime` int(11) DEFAULT NULL COMMENT '修改时间',
  `content` longtext COMMENT '内容',
  `tpl_category` varchar(50) DEFAULT NULL COMMENT '栏目模型',
  `tpl_post` varchar(50) DEFAULT NULL COMMENT '内容模型',
  `page` int(255) NOT NULL DEFAULT '10' COMMENT '页码',
  `sname` varchar(100) DEFAULT NULL COMMENT '缩写标题',
  `keyword` varchar(255) DEFAULT NULL COMMENT '关键词',
  `target` varchar(20) DEFAULT NULL COMMENT '跳转类型',
  `append` varchar(255) DEFAULT NULL COMMENT '补充',
  `file` varchar(255) DEFAULT NULL COMMENT '文件',
  `token` varchar(255) NOT NULL DEFAULT '' COMMENT 'token',
  `seo_t` varchar(255) DEFAULT NULL COMMENT 'Seo标题',
  `seo_d` varchar(255) DEFAULT NULL COMMENT 'Seo描述',
  `seo_k` varchar(255) DEFAULT NULL COMMENT 'Seo关键词',
  `main_keys_m` varchar(255) DEFAULT NULL COMMENT '主关键词',
  `main_keys_c` varchar(255) DEFAULT NULL COMMENT '次要关键词',
  `tags` varchar(255) DEFAULT NULL COMMENT '标签',
  `bd_sl` varchar(255) DEFAULT '0' COMMENT '百度是否收录',
  `alt_pic` varchar(255) DEFAULT NULL COMMENT '图片alt',
  `banner` varchar(255) DEFAULT NULL COMMENT 'banner',
  `alt_banner` varchar(255) DEFAULT NULL COMMENT 'banner alt',
  `type` varchar(255) DEFAULT '' COMMENT '类型',
  `lang` varchar(50) NOT NULL DEFAULT '' COMMENT '语言',
  `lang_pid` int(11) NOT NULL DEFAULT '0' COMMENT '语言pid',
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='分类表';

-- ----------------------------
-- Records of zf_category
-- ----------------------------
-- ----------------------------
-- Table structure for zf_category_model
-- ----------------------------
DROP TABLE IF EXISTS `zf_category_model`;
CREATE TABLE `zf_category_model` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `model` varchar(25) NOT NULL,
  `sort` tinyint(255) NOT NULL DEFAULT '0',
  `status` tinyint(5) NOT NULL DEFAULT '0',
  `is_two` tinyint(1) NOT NULL DEFAULT '0',
  `is_parm` int(1) NOT NULL DEFAULT '1',
  `token` varchar(255) NOT NULL DEFAULT '',
  `lang` varchar(50) NOT NULL DEFAULT '',
  `lang_pid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='分类模型表';

-- ----------------------------
-- Records of zf_category_model
-- ----------------------------
INSERT INTO `zf_category_model` VALUES ('1', '单页模型', 'simple', '2', '1', '1', '0', '', '', '0');
INSERT INTO `zf_category_model` VALUES ('2', '新闻模型', 'news', '0', '1', '1', '0', '', '', '0');
INSERT INTO `zf_category_model` VALUES ('3', 'ZF内容模型', 'zf_tpl', '2', '0', '1', '1', '', '', '0');
INSERT INTO `zf_category_model` VALUES ('9', '论坛模型', 'bbs', '0', '1', '0', '1', '', '', '0');

-- ----------------------------
-- Table structure for zf_category_model_parm
-- ----------------------------
DROP TABLE IF EXISTS `zf_category_model_parm`;
CREATE TABLE `zf_category_model_parm` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `position` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1左  2右',
  `name` varchar(50) NOT NULL,
  `key` varchar(50) NOT NULL,
  `value` text NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'layui-input',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `readonly` tinyint(1) NOT NULL DEFAULT '0',
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `mid` int(11) NOT NULL DEFAULT '0',
  `sort` tinyint(5) NOT NULL DEFAULT '0',
  `is_multi` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否多选   0 否   1 多选',
  `token` varchar(255) NOT NULL DEFAULT '',
  `theme` tinyint(2) NOT NULL DEFAULT '1',
  `append1` text NOT NULL,
  `append2` text NOT NULL,
  `append3` text NOT NULL,
  `lang` varchar(50) NOT NULL DEFAULT '',
  `lang_pid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8 COMMENT='模型参数列表';

-- ----------------------------
-- Records of zf_category_model_parm
-- ----------------------------
INSERT INTO `zf_category_model_parm` VALUES ('2', '1', '图集', 'album', '', 'filesystem_album', '1', '0', '0', '2', '3', '1', '', '1', '', '', '', '', '0');
INSERT INTO `zf_category_model_parm` VALUES ('17', '1', '标题', 'title', '', 'form_input', '1', '0', '0', '2', '1', '0', '', '1', '', '', '', '', '0');
INSERT INTO `zf_category_model_parm` VALUES ('18', '1', '简介', 'summary', '', 'form_textarea', '1', '0', '0', '2', '2', '0', '', '1', '', '', '', '', '0');
INSERT INTO `zf_category_model_parm` VALUES ('19', '2', '图片', 'pic', '', 'filesystem_pic', '1', '0', '0', '2', '4', '0', '', '1', '', '', '', '', '0');
INSERT INTO `zf_category_model_parm` VALUES ('20', '2', '排序', 'sort', '', 'form_input', '1', '0', '0', '2', '3', '0', '', '1', '', '', '', '', '0');
INSERT INTO `zf_category_model_parm` VALUES ('21', '2', '时间', 'ctime', '', 'form_time', '1', '0', '0', '2', '2', '0', '', '1', 'datetime', '', '', '', '0');
INSERT INTO `zf_category_model_parm` VALUES ('22', '1', '详情', 'content', '', 'form_tinymce', '1', '0', '0', '2', '4', '0', '', '1', '', '', '', '', '0');
INSERT INTO `zf_category_model_parm` VALUES ('23', '2', '作者', 'author', '', 'form_input', '1', '0', '0', '2', '1', '0', '', '1', '', '', '', '', '0');
INSERT INTO `zf_category_model_parm` VALUES ('39', '1', 'Tag', 'tags', '', 'form_textarea', '1', '0', '0', '2', '2', '0', '', '1', '', '', '', '', '0');
INSERT INTO `zf_category_model_parm` VALUES ('40', '1', '标题', 'title', '', 'layui-input', '1', '0', '0', '9', '0', '0', '', '1', '', '', '', '', '0');
INSERT INTO `zf_category_model_parm` VALUES ('41', '1', '内容', 'content', '', 'form_ueditor', '1', '0', '0', '9', '0', '0', '', '1', '', '', '', '', '0');
INSERT INTO `zf_category_model_parm` VALUES ('42', '1', '类型', 'type', 'a1,a2,a3,a4', 'form_input', '1', '0', '0', '9', '0', '0', '', '1', '', '', '', '', '0');
INSERT INTO `zf_category_model_parm` VALUES ('43', '1', '用户', 'author', '', 'layui-input', '1', '0', '0', '9', '0', '0', '', '1', '', '', '', '', '0');

-- ----------------------------
-- Table structure for zf_config
-- ----------------------------
DROP TABLE IF EXISTS `zf_config`;
CREATE TABLE `zf_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL,
  `value` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `msg` varchar(255) DEFAULT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'user' COMMENT 'user 用户   system系统',
  `sort` int(11) NOT NULL DEFAULT '0',
  `token` varchar(255) NOT NULL DEFAULT '',
  `lang` varchar(50) NOT NULL DEFAULT '',
  `lang_pid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='系统变量表';

-- ----------------------------
-- Records of zf_config
-- ----------------------------
INSERT INTO `zf_config` VALUES ('1', 'zf_tpl_suffix', '', '1', '前台模板', 'system', '0', '', '', '0');
INSERT INTO `zf_config` VALUES ('2', 'lang', '', '1', '语言版本', 'system', '0', '', '', '0');

-- ----------------------------
-- Table structure for zf_guessbook
-- ----------------------------
DROP TABLE IF EXISTS `zf_guessbook`;
CREATE TABLE `zf_guessbook` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `summary` varchar(255) NOT NULL,
  `content` varchar(255) NOT NULL,
  `sort` tinyint(2) NOT NULL DEFAULT '0',
  `ctime` int(11) NOT NULL DEFAULT '0',
  `tel` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `token` varchar(255) NOT NULL DEFAULT '',
  `company` varchar(255) DEFAULT NULL,
  `ctype` varchar(255) DEFAULT NULL,
  `yusuan` varchar(255) DEFAULT NULL,
  `lang` varchar(50) NOT NULL DEFAULT '',
  `lang_pid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='留言表';

-- ----------------------------
-- Records of zf_guessbook
-- ----------------------------

-- ----------------------------
-- Table structure for zf_hook
-- ----------------------------
DROP TABLE IF EXISTS `zf_hook`;
CREATE TABLE `zf_hook` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `controller` varchar(255) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `sort` int(5) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `overlay` tinyint(1) NOT NULL DEFAULT '0',
  `ctime` varchar(50) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `lang` varchar(50) NOT NULL DEFAULT '',
  `lang_pid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zf_hook
-- ----------------------------
-- ----------------------------
-- Table structure for zf_link
-- ----------------------------
DROP TABLE IF EXISTS `zf_link`;
CREATE TABLE `zf_link` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  `target` varchar(255) DEFAULT NULL,
  `sort` int(5) DEFAULT '0',
  `summary` varchar(255) DEFAULT NULL,
  `token` varchar(255) NOT NULL DEFAULT '',
  `nofollow` tinyint(1) NOT NULL DEFAULT '1',
  `lang` varchar(50) NOT NULL DEFAULT '',
  `lang_pid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='超链表';

-- ----------------------------
-- Records of zf_link
-- ----------------------------
INSERT INTO `zf_link` VALUES ('3', '王明昌博客', 'https://www.wangmingchang.com/', '1', '1', '1614232437', null, '0', '', '', '0', '', '0');

-- ----------------------------
-- Table structure for zf_menu
-- ----------------------------
DROP TABLE IF EXISTS `zf_menu`;
CREATE TABLE `zf_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `action` varchar(10) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `sort` int(5) NOT NULL DEFAULT '0',
  `r_sort` int(5) NOT NULL DEFAULT '0',
  `pic` varchar(255) DEFAULT NULL,
  `menu_pid` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `tag` varchar(255) DEFAULT NULL,
  `summary` varchar(255) DEFAULT NULL,
  `cname` varchar(50) DEFAULT NULL,
  `lang` varchar(50) NOT NULL DEFAULT '',
  `lang_pid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='菜单表';

-- ----------------------------
-- Records of zf_menu
-- ----------------------------
-- ----------------------------
-- Table structure for zf_plugin
-- ----------------------------
DROP TABLE IF EXISTS `zf_plugin`;
CREATE TABLE `zf_plugin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `plugin_name` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `ctime` varchar(50) DEFAULT NULL,
  `utime` int(50) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 正常   2 未激活  9 停止使用',
  `sort` varchar(255) DEFAULT NULL,
  `version` varchar(255) DEFAULT NULL,
  `pic` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `source` varchar(255) NOT NULL,
  `soft_id` varchar(50) NOT NULL,
  `soft_sc` text NOT NULL,
  `soft_key` varchar(255) NOT NULL DEFAULT '',
  `token` varchar(255) NOT NULL DEFAULT '',
  `lang` varchar(50) NOT NULL DEFAULT '',
  `lang_pid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='插件列表';

-- ----------------------------
-- Records of zf_plugin
-- ----------------------------

-- ----------------------------
-- Table structure for zf_plugin_data
-- ----------------------------
DROP TABLE IF EXISTS `zf_plugin_data`;
CREATE TABLE `zf_plugin_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `plugin_name` varchar(255) DEFAULT NULL,
  `ctime` int(11) NOT NULL DEFAULT '0',
  `sort` int(5) NOT NULL DEFAULT '0',
  `tag` varchar(255) NOT NULL,
  `data` longtext,
  `type` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zf_plugin_data
-- ----------------------------

-- ----------------------------
-- Table structure for zf_post
-- ----------------------------
DROP TABLE IF EXISTS `zf_post`;
CREATE TABLE `zf_post` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `cid` int NOT NULL,
  `title` text,
  `summary` varchar(255) DEFAULT NULL,
  `pic` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `ctime` int DEFAULT NULL,
  `utime` int DEFAULT NULL,
  `sort` tinyint DEFAULT '0',
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `append` varchar(255) DEFAULT NULL,
  `file` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `album` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `price_new` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tkl` varchar(20) NOT NULL DEFAULT '0',
  `hits` int NOT NULL DEFAULT '0',
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `author` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `openid` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `cj_id` int NOT NULL DEFAULT '0',
  `relevan_id` int NOT NULL DEFAULT '0' COMMENT '关联文章ID  0不关联',
  `is_product` tinyint(1) NOT NULL DEFAULT '0',
  `p_cate` varchar(255) DEFAULT NULL,
  `sku_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 单规格 2 多规格',
  `ini_hits` int NOT NULL DEFAULT '0' COMMENT '初始访问量    总访问量=初始访问量+实际访问量',
  `recommend` tinyint(1) NOT NULL DEFAULT '0',
  `istop` tinyint(1) NOT NULL DEFAULT '0',
  `post_id` int NOT NULL DEFAULT '0',
  `seo_t` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `seo_d` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `seo_k` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `main_keys_m` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `main_keys_c` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `tags` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `token` varchar(255) NOT NULL DEFAULT '',
  `bd_sl` varchar(255) DEFAULT '0',
  `alt_pic` varchar(255) DEFAULT NULL,
  `type` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `lang` varchar(50) NOT NULL DEFAULT '',
  `lang_pid` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COMMENT='内容表';

-- ----------------------------
-- Records of zf_post
-- ----------------------------

-- ----------------------------
-- Table structure for zf_post_reply
-- ----------------------------
DROP TABLE IF EXISTS `zf_post_reply`;
CREATE TABLE `zf_post_reply` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  `zan` int(5) NOT NULL DEFAULT '0',
  `ip` varchar(50) DEFAULT NULL,
  `reply_uid` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `adopt` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `sort` int(5) NOT NULL DEFAULT '0',
  `token` varchar(255) NOT NULL DEFAULT '',
  `lang` varchar(50) NOT NULL DEFAULT '',
  `lang_pid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章回复';

-- ----------------------------
-- Records of zf_post_reply
-- ----------------------------

-- ----------------------------
-- Table structure for zf_special
-- ----------------------------
DROP TABLE IF EXISTS `zf_special`;
CREATE TABLE `zf_special` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  `sort` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `pic` varchar(255) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `seo_t` varchar(255) DEFAULT NULL,
  `seo_d` varchar(255) DEFAULT NULL,
  `seo_k` varchar(255) DEFAULT NULL,
  `main_keys_m` varchar(255) DEFAULT NULL,
  `main_keys_c` varchar(255) DEFAULT NULL,
  `summary` varchar(255) DEFAULT NULL,
  `hits` int(11) NOT NULL DEFAULT '0',
  `content` longtext,
  `tags` varchar(2000) DEFAULT NULL,
  `bd_sl` tinyint(1) NOT NULL DEFAULT '0',
  `lang` varchar(50) NOT NULL DEFAULT '',
  `lang_pid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='专题表';

-- ----------------------------
-- Records of zf_special
-- ----------------------------
INSERT INTO `zf_special` VALUES ('1', '专题1', '1634631891', '12', '1', '', null, 'qqqwaw', '', '', '', '', '', '30', null, null, '0', '', '0');

-- ----------------------------
-- Table structure for zf_special_post
-- ----------------------------
DROP TABLE IF EXISTS `zf_special_post`;
CREATE TABLE `zf_special_post` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(11) DEFAULT NULL,
  `special_id` int(11) DEFAULT NULL,
  `token` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `ctime` int(11) DEFAULT NULL,
  `lang` varchar(50) NOT NULL DEFAULT '',
  `lang_pid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='专题关联表';

-- ----------------------------
-- Records of zf_special_post
-- ----------------------------

-- ----------------------------
-- Table structure for zf_tag
-- ----------------------------
DROP TABLE IF EXISTS `zf_tag`;
CREATE TABLE `zf_tag` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  `sort` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `pic` varchar(255) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `seo_t` varchar(255) DEFAULT NULL,
  `seo_d` varchar(255) DEFAULT NULL,
  `seo_k` varchar(255) DEFAULT NULL,
  `main_keys_m` varchar(255) DEFAULT NULL,
  `main_keys_c` varchar(255) DEFAULT NULL,
  `summary` varchar(255) DEFAULT NULL,
  `hits` int(11) NOT NULL DEFAULT '0',
  `content` longtext,
  `bd_sl` tinyint(1) NOT NULL,
  `lang` varchar(50) NOT NULL DEFAULT '',
  `lang_pid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='标签表';

-- ----------------------------
-- Records of zf_tag
-- ----------------------------
INSERT INTO `zf_tag` VALUES ('1', '标签1', '1634632005', '12', '1', '', null, '标签1', '', '', '', '', '', '5', null, '0', '', '0');

-- ----------------------------
-- Table structure for zf_task
-- ----------------------------
DROP TABLE IF EXISTS `zf_task`;
CREATE TABLE `zf_task` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `data` varchar(255) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  `etime` int(11) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT '0',
  `stime` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `token` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `utime` int(11) DEFAULT NULL,
  `interval_time` int(11) NOT NULL DEFAULT '60',
  `tag` varchar(255) DEFAULT NULL,
  `hours` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zf_task
-- ----------------------------
-- ----------------------------
-- Table structure for zf_task_log
-- ----------------------------
DROP TABLE IF EXISTS `zf_task_log`;
CREATE TABLE `zf_task_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(11) DEFAULT NULL,
  `task_data` text,
  `ret_data` text,
  `ctime` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zf_task_log
-- ----------------------------

-- ----------------------------
-- Table structure for zf_upload
-- ----------------------------
DROP TABLE IF EXISTS `zf_upload`;
CREATE TABLE `zf_upload` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `uid` varchar(50) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `cid` int(11) DEFAULT NULL,
  `mine` varchar(255) DEFAULT NULL,
  `size` varchar(100) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `uniacid` int(11) DEFAULT NULL,
  `session_data` text,
  `token` varchar(255) NOT NULL DEFAULT '',
  `lang` varchar(50) NOT NULL DEFAULT '',
  `lang_pid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='上传表';

-- ----------------------------
-- Records of zf_upload
-- ----------------------------

-- ----------------------------
-- Table structure for zf_upload_cate
-- ----------------------------
DROP TABLE IF EXISTS `zf_upload_cate`;
CREATE TABLE `zf_upload_cate` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `pid` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `sort` varchar(255) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  `uniacid` int(11) DEFAULT NULL,
  `token` varchar(255) NOT NULL DEFAULT '',
  `lang` varchar(50) NOT NULL DEFAULT '',
  `lang_pid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='上传分类表';

-- ----------------------------
-- Records of zf_upload_cate
-- ----------------------------
INSERT INTO `zf_upload_cate` VALUES ('1', '分类1', '0', '1', null, null, null, '', '', '0');

-- ----------------------------
-- Table structure for zf_user
-- ----------------------------
DROP TABLE IF EXISTS `zf_user`;
CREATE TABLE `zf_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 NOT NULL,
  `pwd` varchar(250) NOT NULL,
  `tel` varchar(12) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `age` tinyint(3) DEFAULT NULL,
  `sex` tinyint(1) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `gid` tinyint(2) NOT NULL DEFAULT '1',
  `ip` varchar(50) DEFAULT NULL,
  `login_num` int(11) NOT NULL DEFAULT '0',
  `ctime` int(11) DEFAULT NULL,
  `sort` tinyint(5) NOT NULL DEFAULT '1',
  `pic` varchar(255) NOT NULL DEFAULT 'https://i.loli.net/2019/11/01/yl8rCLRnOuz7k4g.png',
  `nickName` varchar(50) NOT NULL,
  `avatarUrl` varchar(255) NOT NULL,
  `openid` varchar(50) NOT NULL,
  `api_key` varchar(255) NOT NULL,
  `login_act_code` varchar(50) NOT NULL,
  `utime` int(11) NOT NULL,
  `type` varchar(10) NOT NULL,
  `appid` varchar(255) DEFAULT NULL,
  `github_openid` varchar(255) DEFAULT NULL,
  `login_time` int(11) DEFAULT NULL,
  `token` varchar(255) NOT NULL DEFAULT '',
  `integral` double(255,2) NOT NULL DEFAULT '0.00',
  `wx` varchar(255) DEFAULT NULL,
  `qq` varchar(255) DEFAULT NULL,
  `sign` varchar(255) DEFAULT NULL,
  `lang` varchar(50) NOT NULL DEFAULT '',
  `lang_pid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

-- ----------------------------
-- Records of zf_user
-- ----------------------------

-- ----------------------------
-- Table structure for zf_user_group
-- ----------------------------
DROP TABLE IF EXISTS `zf_user_group`;
CREATE TABLE `zf_user_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `ctime` int(11) NOT NULL DEFAULT '0',
  `sort` tinyint(5) NOT NULL DEFAULT '1',
  `token` varchar(255) NOT NULL DEFAULT '',
  `lang` varchar(50) NOT NULL DEFAULT '',
  `lang_pid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='用户分组表';

-- ----------------------------
-- Records of zf_user_group
-- ----------------------------
INSERT INTO `zf_user_group` VALUES ('1', '高级会员', '1', '1538127552', '0', '', '', '0');
INSERT INTO `zf_user_group` VALUES ('2', '普通会员', '1', '1538127552', '0', '', '', '0');

-- ----------------------------
-- Table structure for zf_user_login_log
-- ----------------------------
DROP TABLE IF EXISTS `zf_user_login_log`;
CREATE TABLE `zf_user_login_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `token` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of zf_user_login_log
-- ----------------------------

-- ----------------------------
-- Table structure for zf_user_msg
-- ----------------------------
DROP TABLE IF EXISTS `zf_user_msg`;
CREATE TABLE `zf_user_msg` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  `append` varchar(255) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `token` varchar(255) NOT NULL DEFAULT '',
  `lang` varchar(50) NOT NULL DEFAULT '',
  `lang_pid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户消息表';

-- ----------------------------
-- Records of zf_user_msg
-- ----------------------------

-- ----------------------------
-- Table structure for zf_user_oauth
-- ----------------------------
DROP TABLE IF EXISTS `zf_user_oauth`;
CREATE TABLE `zf_user_oauth` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `app_id` varchar(255) DEFAULT NULL,
  `pic` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `data` text,
  `ctime` int(11) NOT NULL DEFAULT '0',
  `utime` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `token` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zf_user_oauth
-- ----------------------------
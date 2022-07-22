/*
 Navicat MySQL Data Transfer

 Source Server         : @@@@本地zfcms
 Source Server Type    : MySQL
 Source Server Version : 50734
 Source Host           : localhost:8889
 Source Schema         : zfcms

 Target Server Type    : MySQL
 Target Server Version : 50734
 File Encoding         : 65001

 Date: 13/03/2022 22:28:17
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='管理员表';

-- ----------------------------
-- Records of zf_admin
-- ----------------------------
BEGIN;
INSERT INTO `zf_admin` VALUES (1, 1, 'admin', '', '', '', 0, 1, '23', 1, 0, '', 0, NULL, 0, '76DGRK3PKWYNP23Z', 0, 'http://oss002.wangmingchang.com/uploads/397936734957a99a5aff68e9c62e62fd/20210818_772390_c53495d1c7fc87064d38912ba538f887.jpg', '1646748339', 1, 'oPMmW5v11jyHKr5PA7IanuzavkY8', 'b1393f98b99333301f6cc92c8fbeed12');
COMMIT;

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='管理员分组表';

-- ----------------------------
-- Records of zf_admin_group
-- ----------------------------
BEGIN;
INSERT INTO `zf_admin_group` VALUES (1, '超级管理员', 1, NULL, 1, '180,182,183,184,185,193,197,210,211,212,213,186,187,188,170,171,172,173,174,175,176,177,178,179,154,157,163,164,167,158,161,162,159,165,166,168,160,169,201,202,203,194,195,196,189,190,191,192,204,205,198,199,200,209,206,207,208', '');
INSERT INTO `zf_admin_group` VALUES (2, '普通管理员', 1, NULL, 1, '181,180,182,183,184,185,186,187,188,170,171,172,173,174,175,176,177,178,179,189,190,191,192,154,157,163,164,167,158,161,162,159,165,166,168,160,169', '');

COMMIT;

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='后台访问日志表';

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
  `control` varchar(50) NOT NULL,
  `act` varchar(50) NOT NULL,
  `menu` tinyint(1) NOT NULL DEFAULT '1',
  `parm` varchar(255) DEFAULT NULL,
  `token` varchar(255) NOT NULL DEFAULT '',
  `icon` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=216 DEFAULT CHARSET=utf8 COMMENT='管理员权限表';

-- ----------------------------
-- Records of zf_admin_role
-- ----------------------------
BEGIN;
INSERT INTO `zf_admin_role` VALUES (154, '网站设置', 'admin/Config/', 1, 1, NULL, 50, 180, 'admin', 'Config', '', 1, '', '1638408660', 'fa fa-cog');
INSERT INTO `zf_admin_role` VALUES (157, '管理组', 'admin/Config/admin_group', 1, 1, NULL, 0, 154, 'admin', 'Config', 'admin_group', 1, '', '1638408991', 'fa fa-users');
INSERT INTO `zf_admin_role` VALUES (158, '管理员列表', 'admin/Config/admin_index', 1, 1, NULL, 0, 154, 'admin', 'Config', 'admin_index', 1, '', '1638409004', 'fa fa-user-secret');
INSERT INTO `zf_admin_role` VALUES (159, '权限管理', 'admin/Config/admin_role', 1, 1, NULL, 0, 154, 'admin', 'Config', 'admin_role', 1, '', '1638409120', 'fa fa-american-sign-language-interpreting');
INSERT INTO `zf_admin_role` VALUES (160, '操作日志', 'admin/Config/action_log', 1, 1, NULL, 0, 154, 'admin', 'Config', 'action_log', 1, '', '1638409211', 'fa fa-bandcamp');
INSERT INTO `zf_admin_role` VALUES (161, '新增管理员', 'admin/Config/admin_add', 1, 1, NULL, 0, 158, 'admin', 'Config', 'admin_add', 0, '', '', '');
INSERT INTO `zf_admin_role` VALUES (162, '编辑管理员', 'admin/Config/admin_edit', 1, 1, NULL, 0, 158, 'admin', 'Config', 'admin_edit', 0, '', '', '');
INSERT INTO `zf_admin_role` VALUES (163, '新增管理组', 'admin/Config/admin_group_add', 1, 1, NULL, 0, 157, 'admin', 'Config', 'admin_group_add', 0, '', '', NULL);
INSERT INTO `zf_admin_role` VALUES (164, '编辑管理组', 'admin/Config/admin_group_edit', 1, 1, NULL, 0, 157, 'admin', 'Config', 'admin_group_edit', 0, '', '', NULL);
INSERT INTO `zf_admin_role` VALUES (165, '新增权限', 'admin/Config/admin_role_add', 1, 1, NULL, 0, 159, 'admin', 'Config', 'admin_role_add', 0, '', '', NULL);
INSERT INTO `zf_admin_role` VALUES (166, '编辑权限', 'admin/Config/admin_role_edit', 1, 1, NULL, 0, 159, 'admin', 'Config', 'admin_role_edit', 0, '', '', NULL);
INSERT INTO `zf_admin_role` VALUES (167, '管理组权限', 'admin/Config/admin_group_role', 1, 1, NULL, 0, 157, 'admin', 'Config', 'admin_group_role', 0, '', '', NULL);
INSERT INTO `zf_admin_role` VALUES (168, '获取方法', 'admin/Config/get_action', 1, 1, NULL, 0, 159, 'admin', 'Config', 'get_action', 0, '', '', NULL);
INSERT INTO `zf_admin_role` VALUES (169, 'OSS设置', 'admin/Config/oss_config', 1, 1, NULL, 0, 154, 'admin', 'Config', 'oss_config', 1, '', '1638409235', 'fa fa-arrow-circle-up');
INSERT INTO `zf_admin_role` VALUES (170, '其他设置', 'admin/Rests/', 1, 1, NULL, 30, 180, 'admin', 'Rests', '', 1, '', '1638408588', 'fa fa-cubes');
INSERT INTO `zf_admin_role` VALUES (171, '广告管理', 'admin/Rests/advert', 1, 1, NULL, 0, 170, 'admin', 'Rests', 'advert', 1, '', '1634719759', 'fa fa-adjust');
INSERT INTO `zf_admin_role` VALUES (172, '新增广告', 'admin/Rests/advert_add', 1, 1, NULL, 0, 171, 'admin', 'Rests', 'advert_add', 0, '', '', NULL);
INSERT INTO `zf_admin_role` VALUES (173, '编辑广告', 'admin/Rests/advert_edit', 1, 1, NULL, 0, 171, 'admin', 'Rests', 'advert_edit', 0, '', '', NULL);
INSERT INTO `zf_admin_role` VALUES (174, '超链管理', 'admin/Rests/link', 1, 1, NULL, 0, 170, 'admin', 'Rests', 'link', 1, '', '1634719773', 'fa fa-link');
INSERT INTO `zf_admin_role` VALUES (175, '新增超链', 'admin/Rests/link_add', 1, 1, NULL, 0, 174, 'admin', 'Rests', 'link_add', 0, '', '', NULL);
INSERT INTO `zf_admin_role` VALUES (176, '编辑超链', 'admin/Rests/link_edit', 1, 1, NULL, 0, 174, 'admin', 'Rests', 'link_edit', 0, '', '', NULL);
INSERT INTO `zf_admin_role` VALUES (177, '留言管理', 'admin/Rests/guessbook', 1, 1, NULL, 0, 170, 'admin', 'Rests', 'guessbook', 1, '', '1634719799', 'fa fa-align-justify');
INSERT INTO `zf_admin_role` VALUES (178, '新增留言', 'admin/Rests/guessbook_add', 1, 1, NULL, 0, 177, 'admin', 'Rests', 'guessbook_add', 0, '', '', NULL);
INSERT INTO `zf_admin_role` VALUES (179, '留言详情', 'admin/Rests/guessbook_edit', 1, 1, NULL, 0, 177, 'admin', 'Rests', 'guessbook_edit', 0, '', '', NULL);
INSERT INTO `zf_admin_role` VALUES (180, '内容管理', 'admin/Category/', 1, 1, NULL, 10, 0, 'admin', 'Category', '', 1, '', '', 'fa fa-book');
INSERT INTO `zf_admin_role` VALUES (182, '内容板块', 'admin/Category/index', 1, 1, NULL, 0, 180, 'admin', 'Category', 'index', 1, '', '1646270646', 'fa fa-book');
INSERT INTO `zf_admin_role` VALUES (183, '内容模型', 'admin/Category/category_model', 1, 1, NULL, 0, 180, 'admin', 'Category', 'category_model', 1, '', '', 'fa fa-modx');
INSERT INTO `zf_admin_role` VALUES (184, '内容列表', 'admin/Category/post_all_list', 1, 1, NULL, 0, 180, 'admin', 'Category', 'post_all_list', 1, '', '', 'fa fa-book');
INSERT INTO `zf_admin_role` VALUES (185, '专题列表', 'admin/Category/special', 1, 1, NULL, 0, 180, 'admin', 'Category', 'special', 1, '', '1634719563', 'fa fa-book');
INSERT INTO `zf_admin_role` VALUES (186, '用户管理', 'admin/User/', 1, 1, NULL, 20, 180, 'admin', 'User', '', 1, '', '1638408530', 'fa fa-user-circle');
INSERT INTO `zf_admin_role` VALUES (187, '用户列表', 'admin/User/index', 1, 1, NULL, 0, 186, 'admin', 'User', 'index', 1, '', '1634719634', 'fa fa-user');
INSERT INTO `zf_admin_role` VALUES (188, '用户分组', 'admin/User/group', 1, 1, NULL, 0, 186, 'admin', 'User', 'group', 1, '', '1634719658', 'fa fa-users');
INSERT INTO `zf_admin_role` VALUES (193, 'Tag标签', 'admin/Category/tag', 1, 1, NULL, 0, 180, 'admin', 'Category', 'tag', 1, '', '1634719581', 'fa fa-tags');
INSERT INTO `zf_admin_role` VALUES (195, '分组', 'plugins/zf_querylist.index/cate', 1, 1, NULL, 0, 194, 'plugins', 'index', 'cate', 1, '', '', '');
INSERT INTO `zf_admin_role` VALUES (196, '列表', 'plugins/zf_querylist.index/index', 1, 1, NULL, 0, 194, 'plugins', 'index', 'index', 1, '', '', '');
INSERT INTO `zf_admin_role` VALUES (198, '商店', 'admin/0/0', 1, 1, NULL, 80, 0, 'admin', '0', '0', 1, '', '1637805157', '');
INSERT INTO `zf_admin_role` VALUES (199, '模板', 'admin/Zfyun/themes', 1, 1, NULL, 0, 198, 'admin', 'Zfyun', 'themes', 1, '', '1634719930', 'fa fa-align-justify');
INSERT INTO `zf_admin_role` VALUES (200, '插件', 'admin/Zfyun/plugins', 1, 1, NULL, 0, 198, 'admin', 'Zfyun', 'plugins', 1, '', '1634719907', 'fa fa-plug');
INSERT INTO `zf_admin_role` VALUES (201, '其他参数', 'admin/Config/custom_config', 1, 1, NULL, 0, 154, 'admin', 'Config', 'custom_config', 1, '', '1638409253', 'fa fa-sticky-note-o');
INSERT INTO `zf_admin_role` VALUES (202, '基本设置', 'admin/Config/index', 1, 1, NULL, 0, 154, 'admin', 'Config', 'index', 1, '', '1638409341', 'fa fa-cube');
INSERT INTO `zf_admin_role` VALUES (203, '邮箱配置', 'admin/Config/email', 1, 1, NULL, 0, 154, 'admin', 'Config', 'email', 1, '', '1638409286', 'fa fa-window-maximize');
INSERT INTO `zf_admin_role` VALUES (209, '升级', 'admin/Zfyun/upgrade', 1, 1, NULL, 0, 198, 'admin', 'Zfyun', 'ZfUpgrade', 1, '', '1637822193', 'fa fa-gratipay');
INSERT INTO `zf_admin_role` VALUES (213, '菜单管理', 'admin/Rests/menu', 1, 1, NULL, 0, 170, 'admin', 'Rests', 'menu', 1, '', '1644927259', 'fa fa-certificate');
INSERT INTO `zf_admin_role` VALUES (214, '论坛板块', 'admin/Category/index', 1, 1, NULL, 0, 180, 'admin', 'Category', 'index', 1, 'type=bbs', '1646224208', 'fa fa-500px');
INSERT INTO `zf_admin_role` VALUES (215, '在线商店', 'admin/Zfyun/store', 1, 1, NULL, 0, 198, 'admin', 'Zfyun', 'store', 1, '', '1646401835', 'fa fa-window-restore');
COMMIT;

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='广告表';

-- ----------------------------
-- Table structure for zf_category
-- ----------------------------
DROP TABLE IF EXISTS `zf_category`;
CREATE TABLE `zf_category` (
  `cid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0',
  `mid` tinyint(1) NOT NULL DEFAULT '1',
  `name` varchar(30) NOT NULL COMMENT '标题',
  `ename` varchar(30) DEFAULT NULL COMMENT '英文名',
  `cname` varchar(255) DEFAULT NULL,
  `summary` varchar(255) DEFAULT NULL COMMENT '简介',
  `pic` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `url` varchar(150) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `sort` tinyint(4) NOT NULL DEFAULT '0',
  `menu` tinyint(1) NOT NULL DEFAULT '0',
  `ctime` int(11) DEFAULT NULL,
  `utime` int(11) DEFAULT NULL,
  `content` longtext,
  `tpl_category` varchar(50) DEFAULT NULL,
  `tpl_post` varchar(50) DEFAULT NULL,
  `page` int(255) NOT NULL DEFAULT '10',
  `sname` varchar(100) DEFAULT NULL,
  `keyword` varchar(255) DEFAULT NULL,
  `target` varchar(20) DEFAULT NULL,
  `append` varchar(255) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `token` varchar(255) NOT NULL DEFAULT '',
  `seo_t` varchar(255) DEFAULT NULL,
  `seo_d` varchar(255) DEFAULT NULL,
  `seo_k` varchar(255) DEFAULT NULL,
  `main_keys_m` varchar(255) DEFAULT NULL,
  `main_keys_c` varchar(255) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `bd_sl` varchar(255) DEFAULT '0',
  `alt_pic` varchar(255) DEFAULT NULL,
  `banner` varchar(255) DEFAULT NULL,
  `alt_banner` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT '',
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='分类表';

-- ----------------------------
-- Records of zf_category
-- ----------------------------
BEGIN;
INSERT INTO `zf_category` VALUES (1, 0, 6, '未分类', '', NULL, '', '', '', '', 0, 0, 0, 1639016123, 1643531030, NULL, '', '', 10, NULL, NULL, '', NULL, '', '', '', '', '', '', '', NULL, '0', '', '', '', '');
COMMIT;

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='分类模型表';

-- ----------------------------
-- Records of zf_category_model
-- ----------------------------
BEGIN;
INSERT INTO `zf_category_model` VALUES (1, '单页模型', 'simple', 2, 1, 1, 0, '');
INSERT INTO `zf_category_model` VALUES (2, '新闻模型', 'news', 0, 1, 1, 0, '');
INSERT INTO `zf_category_model` VALUES (3, 'ZF内容模型', 'zf_tpl', 2, 0, 1, 1, '');
INSERT INTO `zf_category_model` VALUES (4, '标题&图&详情', 'title_pic_content', 0, 0, 1, 1, '');
INSERT INTO `zf_category_model` VALUES (5, '图&标题', 'pic_title', 0, 0, 1, 1, '');
INSERT INTO `zf_category_model` VALUES (6, '标题&图集', 'album_title', 0, 0, 1, 1, '');
INSERT INTO `zf_category_model` VALUES (7, '标题&图&文件', 'title_pic_file', 0, 0, 0, 1, '');
INSERT INTO `zf_category_model` VALUES (8, '测试ZF模型', 'zf_test', 1, 0, 0, 1, '');
INSERT INTO `zf_category_model` VALUES (9, '论坛模型', 'bbs', 0, 1, 0, 1, '');
COMMIT;

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8 COMMENT='模型参数列表';

-- ----------------------------
-- Records of zf_category_model_parm
-- ----------------------------
BEGIN;
INSERT INTO `zf_category_model_parm` VALUES (2, 1, '图集', 'album', '', 'filesystem_album', 1, 0, 0, 2, 3, 1, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (3, 1, '标题', 'title', '', 'layui-input', 1, 0, 0, 14, 1, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (4, 1, '栏目描述', 'summary', '', 'layui-textarea', 1, 0, 0, 14, 2, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (5, 1, '缩略图', 'album', '', 'album', 0, 0, 0, 14, 3, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (6, 1, '栏目详情', 'content', '', 'ueditor', 1, 0, 0, 14, 4, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (7, 2, '作者', 'author', '', 'layui-input', 1, 0, 0, 14, 1, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (8, 1, '时间', 'ctime', '', 'layui-time', 1, 0, 0, 14, 1, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (9, 2, '排序', 'sort', '', 'layui-input', 1, 0, 0, 14, 0, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (10, 2, '扩展参数', 'append', '', 'layui-radio', 0, 0, 0, 14, 0, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (11, 2, '封面图', 'pic', '', 'layui-pic', 1, 0, 0, 14, 5, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (12, 2, '文件', 'file', '', 'layui-file', 1, 0, 0, 14, 6, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (17, 1, '标题', 'title', '', 'form_input', 1, 0, 0, 2, 1, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (18, 1, '简介', 'summary', '', 'form_textarea', 1, 0, 0, 2, 2, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (19, 2, '图片', 'pic', '', 'filesystem_pic', 1, 0, 0, 2, 4, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (20, 2, '排序', 'sort', '', 'form_input', 1, 0, 0, 2, 3, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (21, 2, '时间', 'ctime', '', 'form_time', 1, 0, 0, 2, 2, 0, '', 1, 'datetime', '', '');
INSERT INTO `zf_category_model_parm` VALUES (22, 1, '详情', 'content', '', 'form_tinymce', 1, 0, 0, 2, 4, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (23, 2, '作者', 'author', '', 'form_input', 1, 0, 0, 2, 1, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (24, 2, '推荐', 'recommend', '', 'layui-switch', 1, 0, 0, 14, 9, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (25, 1, '标题', 'title', '', 'form_input', 1, 0, 0, 4, 1, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (26, 1, '图片', 'pic', '', 'upload_pic', 1, 0, 0, 4, 2, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (27, 1, '详情', 'content', '', 'form_ueditor', 1, 0, 0, 4, 3, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (28, 1, '标题', 'title', '', 'form_input', 1, 0, 0, 5, 1, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (29, 1, '图', 'pic', '', 'upload_pic', 1, 0, 0, 5, 2, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (30, 1, '状态', 'status', '', 'form_radio', 1, 0, 0, 5, 3, 0, '', 1, '[\'0\'=>\'是\',\'1\'=>\'否\']', '', '');
INSERT INTO `zf_category_model_parm` VALUES (31, 1, '标题', 'title', '', 'form_input', 1, 0, 0, 6, 1, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (32, 1, '图集', 'album', '', 'upload_album', 1, 0, 0, 6, 2, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (33, 1, '标题', 'title', '', 'form_input', 1, 0, 0, 8, 1, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (34, 1, '多选1', 'append', '', 'form_radio', 1, 0, 0, 8, 2, 0, '', 1, '[\'0\'=>\'是\',\'1\'=>\'否\']', '', '');
INSERT INTO `zf_category_model_parm` VALUES (35, 1, '描述1', 'summary', '', 'form_input', 1, 0, 0, 8, 3, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (36, 1, '标题', 'title', '', 'layui-input', 1, 0, 0, 7, 0, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (37, 1, '图', 'pic', '', 'upload_pic', 1, 0, 0, 7, 0, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (38, 1, '文件', 'file', '', 'upload_file', 1, 0, 0, 7, 0, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (39, 1, 'Tag', 'tags', '', 'form_textarea', 1, 0, 0, 2, 2, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (40, 1, '标题', 'title', '', 'layui-input', 1, 0, 0, 9, 0, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (41, 1, '内容', 'content', '', 'form_ueditor', 1, 0, 0, 9, 0, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (42, 1, '类型', 'type', 'a1,a2,a3,a4', 'form_input', 1, 0, 0, 9, 0, 0, '', 1, '', '', '');
INSERT INTO `zf_category_model_parm` VALUES (43, 1, '用户', 'author', '', 'layui-input', 1, 0, 0, 9, 0, 0, '', 1, '', '', '');
COMMIT;

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='系统变量表';

-- ----------------------------
-- Records of zf_config
-- ----------------------------
BEGIN;
INSERT INTO `zf_config` VALUES (1, 'zf_tpl_suffix', '', 1, '前台模板', 'system', 0, '');
COMMIT;

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='留言表';

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='超链表';

-- ----------------------------
-- Records of zf_link
-- ----------------------------
BEGIN;
INSERT INTO `zf_link` VALUES (3, '王明昌博客', 'https://www.wangmingchang.com/', '1', '1', 1614232437, NULL, 0, '', '', 0);
COMMIT;

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='菜单表';

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='插件列表';

-- ----------------------------
-- Table structure for zf_post
-- ----------------------------
DROP TABLE IF EXISTS `zf_post`;
CREATE TABLE `zf_post` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cid` int(10) NOT NULL,
  `title` text,
  `summary` varchar(255) DEFAULT NULL,
  `pic` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `ctime` int(11) DEFAULT NULL,
  `utime` int(11) DEFAULT NULL,
  `sort` tinyint(5) DEFAULT '0',
  `content` longtext CHARACTER SET utf8mb4,
  `append` varchar(255) DEFAULT NULL,
  `file` varchar(255) NOT NULL,
  `album` varchar(500) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `price_new` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tkl` varchar(20) NOT NULL DEFAULT '0',
  `hits` int(11) NOT NULL DEFAULT '0',
  `url` varchar(255) NOT NULL,
  `author` varchar(50) NOT NULL,
  `openid` varchar(50) NOT NULL,
  `cj_id` int(11) NOT NULL DEFAULT '0',
  `relevan_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联文章ID  0不关联',
  `is_product` tinyint(1) NOT NULL DEFAULT '0',
  `p_cate` varchar(255) DEFAULT NULL,
  `sku_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 单规格 2 多规格',
  `ini_hits` int(5) NOT NULL DEFAULT '0' COMMENT '初始访问量    总访问量=初始访问量+实际访问量',
  `recommend` tinyint(1) NOT NULL DEFAULT '0',
  `istop` tinyint(1) NOT NULL DEFAULT '0',
  `post_id` int(50) NOT NULL,
  `seo_t` varchar(255) NOT NULL,
  `seo_d` varchar(255) NOT NULL,
  `seo_k` varchar(255) NOT NULL,
  `main_keys_m` varchar(255) NOT NULL,
  `main_keys_c` varchar(255) NOT NULL,
  `tags` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL DEFAULT '',
  `bd_sl` varchar(255) DEFAULT '0',
  `alt_pic` varchar(255) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `ip` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='内容表';

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章回复';

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='专题表';

-- ----------------------------
-- Records of zf_special
-- ----------------------------
BEGIN;
INSERT INTO `zf_special` VALUES (1, '专题1', 1634631891, '12', 0, '', NULL, 'qqqwaw', '', '', '', '', '', 15, NULL, NULL, 0);
COMMIT;

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='专题关联表';

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='标签表';

-- ----------------------------
-- Records of zf_tag
-- ----------------------------
BEGIN;
INSERT INTO `zf_tag` VALUES (1, '标签1', 1634632005, '12', 1, '', NULL, '标签1', '', '', '', '', '', 5, NULL);
COMMIT;

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='上传表';

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='上传分类表';

-- ----------------------------
-- Records of zf_upload_cate
-- ----------------------------
BEGIN;
INSERT INTO `zf_upload_cate` VALUES (1, '分类1', 0, 1, NULL, NULL, NULL, '');
INSERT INTO `zf_upload_cate` VALUES (2, '分类2', 0, 1, NULL, NULL, NULL, '');
COMMIT;

-- ----------------------------
-- Table structure for zf_user
-- ----------------------------
DROP TABLE IF EXISTS `zf_user`;
CREATE TABLE `zf_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='用户分组表';

-- ----------------------------
-- Records of zf_user_group
-- ----------------------------
BEGIN;
INSERT INTO `zf_user_group` VALUES (1, '高级会员', 1, 1538127552, 0, '');
INSERT INTO `zf_user_group` VALUES (2, '普通会员', 1, 1538127552, 0, '');
COMMIT;

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户消息表';

SET FOREIGN_KEY_CHECKS = 1;

-- phpMyAdmin SQL Dump
-- version phpStudy 2014
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2017 �?10 �?24 �?10:44
-- 服务器版本: 5.5.53
-- PHP 版本: 5.6.27

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `action_tp_rbac`
--

-- --------------------------------------------------------

--
-- 表的结构 `action_access`
--

CREATE TABLE IF NOT EXISTS `action_access` (
  `role_id` smallint(6) unsigned NOT NULL,
  `node_id` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) NOT NULL,
  `module` varchar(50) DEFAULT NULL,
  KEY `groupId` (`role_id`),
  KEY `nodeId` (`node_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `action_admin`
--

CREATE TABLE IF NOT EXISTS `action_admin` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `username` varchar(60) NOT NULL COMMENT '用户名',
  `userpass` varchar(60) NOT NULL,
  `realname` varchar(100) NOT NULL,
  `roleid` int(11) NOT NULL,
  `addtime` datetime NOT NULL,
  `logintime` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1正常2锁定',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- 转存表中的数据 `action_admin`
--

INSERT INTO `action_admin` (`id`, `username`, `userpass`, `realname`, `roleid`, `addtime`, `logintime`, `status`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', '邢栋ddddd', 1, '2017-10-16 00:00:00', '2017-10-23 18:10:07', 1),
(2, 'ceshi', 'cc17c30cd111c7215fc8f51f8790e0e1', '测试', 2, '2017-10-17 00:00:00', '2017-10-23 09:38:52', 1),
(3, 'yunying', 'b35ce399132d51dd110949238a9722c1', '运营', 2, '2017-10-18 11:30:20', '2017-10-23 15:46:34', 1);

-- --------------------------------------------------------

--
-- 表的结构 `action_admin_log`
--

CREATE TABLE IF NOT EXISTS `action_admin_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `route` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `node_id` int(11) NOT NULL DEFAULT '0' COMMENT '节点id',
  `user_agent` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `admin_id` int(11) NOT NULL,
  `ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `addtime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `action_node`
--

CREATE TABLE IF NOT EXISTS `action_node` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `controller` varchar(20) NOT NULL COMMENT '控制器',
  `action` varchar(20) NOT NULL COMMENT '方法',
  `title` varchar(50) NOT NULL COMMENT '名称',
  `logo` varchar(50) DEFAULT NULL,
  `show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `remark` varchar(255) DEFAULT NULL,
  `sort` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `pid` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `level` (`level`),
  KEY `pid` (`pid`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- 转存表中的数据 `action_node`
--

INSERT INTO `action_node` (`id`, `controller`, `action`, `title`, `logo`, `show`, `status`, `remark`, `sort`, `pid`, `level`) VALUES
(1, 'Rbac', 'index', '系统管理', ' fa fa-desktop', 1, 1, NULL, 0, 0, 1),
(2, 'Rbac', 'user', '用户列表', '', 1, 1, '', 0, 1, 2),
(3, 'Rbac', 'addUser', '添加用户', NULL, 2, 1, NULL, 0, 1, 2),
(4, 'Rbac', 'editUser', '修改用户', NULL, 2, 1, NULL, 0, 1, 2),
(5, 'Rbac', 'delUser', '删除用户', NULL, 2, 1, NULL, 0, 1, 2),
(6, 'Rbac', 'role', '角色列表', NULL, 1, 1, NULL, 0, 1, 2),
(7, 'Rbac', 'addRole', '添加角色', NULL, 2, 1, NULL, 0, 1, 2),
(8, 'Rbac', 'editRole', '修改角色', NULL, 2, 1, NULL, 0, 1, 2),
(9, 'Rbac', 'assignRole', '角色权限分配', NULL, 2, 1, NULL, 0, 1, 2),
(10, 'Rbac', 'menu', '菜单列表', NULL, 1, 1, NULL, 0, 1, 2),
(11, 'Rbac', 'addMenu', '添加菜单', NULL, 2, 1, NULL, 0, 1, 2),
(12, 'Rbac', 'editMenu', '修改菜单', NULL, 2, 1, NULL, 0, 1, 2),
(13, 'Rbac', 'delMenu', '删除菜单', NULL, 2, 1, NULL, 0, 1, 2);

-- --------------------------------------------------------

--
-- 表的结构 `action_role`
--

CREATE TABLE IF NOT EXISTS `action_role` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `pid` smallint(6) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `remark` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- 转存表中的数据 `action_role`
--

INSERT INTO `action_role` (`id`, `name`, `pid`, `status`, `remark`) VALUES
(1, '管理员', NULL, 1, '管理员'),
(2, '测试', NULL, 1, '测试'),
(3, '运营', NULL, 1, '8d56e59fbb65fedf1d92aed4b0009db7');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

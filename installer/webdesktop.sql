-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 09. August 2011 um 16:35
-- Server Version: 5.1.41
-- PHP-Version: 5.3.2-1ubuntu4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Datenbank: `webdesktop`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `modules`
--

CREATE TABLE IF NOT EXISTS `modules` (
  `m_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `m_moduleId` varchar(32) NOT NULL,
  `m_enabled` tinyint(1) NOT NULL,
  `m_classname` varchar(64) NOT NULL,
  PRIMARY KEY (`m_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `modules`
--

INSERT INTO `modules` (`m_id`, `m_moduleId`, `m_enabled`, `m_classname`) VALUES
(1, 'administration', 1, 'Webdesktop_Model_Modules_Administration'),
(2, 'settings', 1, 'Webdesktop_Model_Modules_Settings');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `style_themes`
--

CREATE TABLE IF NOT EXISTS `style_themes` (
  `sth_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sth_name` varchar(64) NOT NULL,
  `sth_preview` varchar(32) NOT NULL,
  `sth_file` varchar(255) NOT NULL,
  PRIMARY KEY (`sth_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Daten für Tabelle `style_themes`
--

INSERT INTO `style_themes` (`sth_id`, `sth_name`, `sth_preview`, `sth_file`) VALUES
(1, 'Default', 'preview-default.png', 'ext-all.css'),
(2, 'Grey Theme', 'preview-gray.png', 'ext-all-gray.css'),
(3, 'Access Theme', 'preview-access.png', 'ext-all-access.css');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `style_wallpapers`
--

CREATE TABLE IF NOT EXISTS `style_wallpapers` (
  `swp_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `swp_group` varchar(16) NOT NULL,
  `swp_name` varchar(32) NOT NULL,
  `swp_file_thumb` varchar(64) NOT NULL,
  `swp_file` varchar(255) NOT NULL,
  PRIMARY KEY (`swp_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Daten für Tabelle `style_wallpapers`
--

INSERT INTO `style_wallpapers` (`swp_id`, `swp_group`, `swp_name`, `swp_file_thumb`, `swp_file`) VALUES
(1, '-', 'Blue', '-', 'blue.jpg'),
(2, '-', 'Blue Sencha', '-', 'Blue-Sencha.jpg'),
(3, '-', 'Dark Sencha', '-', 'Dark-Sencha.jpg'),
(4, '-', 'Desk', '-', 'desk.jpg'),
(5, '-', 'Desktop', '-', 'desktop.jpg'),
(6, '-', 'Desktop 2', '-', 'desktop2.jpg'),
(7, '-', 'Ext Logo', '-', 'ext.gif'),
(8, '-', 'Shiny', '-', 'shiny.gif'),
(9, '-', 'Sky', '-', 'sky.jpg'),
(10, '-', 'Wood Sencha', '-', 'Wood-Sencha.jpg'),
(11, '', 'None', '', '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_acl_actions`
--

CREATE TABLE IF NOT EXISTS `user_acl_actions` (
  `uaa_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uaa_uamc_id` int(10) unsigned NOT NULL,
  `uaa_action` varchar(64) NOT NULL,
  `uaa_activated` tinyint(1) NOT NULL,
  `uaa_description` varchar(255) NOT NULL,
  PRIMARY KEY (`uaa_id`),
  KEY `uaa_uamc_id` (`uaa_uamc_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=73 ;

--
-- Daten für Tabelle `user_acl_actions`
--

INSERT INTO `user_acl_actions` (`uaa_id`, `uaa_uamc_id`, `uaa_action`, `uaa_activated`, `uaa_description`) VALUES
(1, 1, 'index', 1, 'Default Index Action'),
(2, 2, 'login', 1, 'Login Action for every user'),
(3, 2, 'logout', 1, 'Logout Action for every user'),
(4, 3, 'error', 1, 'Error Action in NOC ErrorController'),
(5, 4, 'index', 1, 'Index Action after Member Login'),
(6, 5, 'index', 1, 'Index Action in AdminController'),
(7, 6, 'scan', 1, 'Scan for actions in a given controller'),
(8, 6, 'edit', 1, 'Edit an Action'),
(9, 6, 'permission', 1, 'change to permission for this action'),
(10, 6, 'status', 1, 'change the status of an action (enable/disable)'),
(11, 6, 'add', 1, 'Add an action from the module/controller'),
(12, 6, 'index', 1, 'list all actions from a modul/controller'),
(13, 6, 'delete', 1, 'Delete an Action in the scan area'),
(14, 7, 'status', 1, 'Change the status of a controller'),
(15, 7, 'edit', 1, 'Edit an controller'),
(16, 7, 'add', 1, 'Add a controller'),
(17, 7, 'delete', 1, 'Delete an controller'),
(18, 7, 'scan', 1, 'Scan module controller directories for new controllers'),
(19, 7, 'index', 1, 'List all known Controllers from the database'),
(20, 8, 'index', 1, 'error index'),
(21, 9, 'index', 1, 'List all available groups'),
(22, 9, 'add', 1, 'Add a Group'),
(23, 9, 'edit', 1, 'Edit a Group'),
(24, 9, 'delete', 1, 'Delete a group virtually'),
(25, 10, 'index', 1, 'list all defined roles'),
(26, 10, 'add', 1, 'create a new role'),
(27, 10, 'edit', 1, 'edit a role and assign users and groups to this role'),
(28, 10, 'status', 1, 'change the status of this role (enable/disable)'),
(29, 10, 'info', 1, 'info card for this role'),
(30, 11, 'index', 1, 'List all available users'),
(31, 11, 'add', 1, 'Add a user'),
(32, 11, 'edit', 1, 'Edit a user'),
(33, 11, 'delete', 1, 'Delete a user (soft delete)'),
(34, 11, 'changepassword', 1, 'Change the password of an user'),
(35, 12, 'index', 1, 'IndexAction of Webdesktop IndexController'),
(36, 13, 'request', 1, 'The Main Request Action Handler for any API Calls in the webdesktop'),
(37, 14, 'index', 1, 'Index Action for Webdesktop Admin Module'),
(38, 14, 'loadUserUsers', 1, 'List all Users in the app'),
(39, 14, 'saveNewUser', 1, 'Create the new User'),
(40, 14, 'saveEditUser', 1, 'Update the User informations'),
(41, 14, 'saveDeleteUser', 1, 'Delete a User'),
(42, 14, 'saveEditUserPw', 1, 'Change the Password for the selected user'),
(43, 14, 'loadGroupGroups', 1, 'Load all Groups for the group listing'),
(44, 14, 'saveNewGroup', 1, 'Create a new Group'),
(45, 14, 'saveEditGroup', 1, 'Update the Group informations'),
(46, 14, 'saveDeleteGroup', 1, 'Delete a Group'),
(47, 14, 'loadRoleRoles', 1, 'Load all Roles'),
(48, 14, 'saveNewRole', 1, 'Create a new Role'),
(49, 14, 'saveEditRole', 1, 'Update the informations for a Role'),
(50, 14, 'saveDeleteRole', 1, 'Delete a role and all referenced entries'),
(51, 14, 'loadControllerControllers', 1, 'Load all Controllers and informations for each controller for the listing grid'),
(52, 14, 'saveAddController', 1, 'Save a new Controllerin the database'),
(53, 14, 'saveEditController', 1, 'Edit a Controller'),
(54, 14, 'saveDeleteController', 1, 'Delete a Controller from the Database'),
(55, 14, 'changeControllerStatus', 1, 'Update the status for a selected controller (enable/disable)'),
(56, 14, 'loadControllerPermissions', 1, 'Load the Permissions for a Controller'),
(57, 14, 'saveControllerPermissions', 1, 'Save the permissions for a controller (all actions from a controller)'),
(58, 14, 'loadControllerActions', 1, 'Load the Actions for a specific Controller'),
(59, 14, 'saveAddAction', 1, 'Add a new scanned Action to the Database'),
(60, 14, 'saveEditAction', 1, 'Edit an Action'),
(61, 14, 'saveDeleteAction', 1, 'Delete an Action from the Database'),
(62, 14, 'changeActionStatus', 1, 'Change the Status for an Action (enable/disable)'),
(63, 14, 'loadActionPermissions', 1, 'Load the Permissions for an Action'),
(64, 14, 'saveActionPermissions', 1, 'Save the Permission for an Action'),
(65, 15, 'index', 1, 'indexAction'),
(66, 15, 'loadWallpapers', 1, 'Load the available Wallpapers'),
(67, 15, 'changeWallpaper', 1, 'Change the User Wallpaper'),
(68, 15, 'loadThemes', 1, 'Load all available themes from the database'),
(69, 15, 'changeTheme', 1, 'Change the user selected theme'),
(70, 15, 'changeShortcuts', 1, 'Save the shortcuts that should be visible on the desktop'),
(71, 15, 'changeAutorun', 1, 'Save the autorun modules that should startup'),
(72, 15, 'changeQuickstart', 1, 'Save the autorun modules that should startup');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_acl_modulecontroller`
--

CREATE TABLE IF NOT EXISTS `user_acl_modulecontroller` (
  `uamc_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uamc_module` varchar(64) NOT NULL,
  `uamc_controller` varchar(64) NOT NULL,
  `uamc_virtual` tinyint(2) unsigned NOT NULL,
  `uamc_activated` tinyint(1) NOT NULL,
  `uamc_description` varchar(255) NOT NULL,
  PRIMARY KEY (`uamc_id`),
  KEY `uamc_module` (`uamc_module`,`uamc_controller`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Daten für Tabelle `user_acl_modulecontroller`
--

INSERT INTO `user_acl_modulecontroller` (`uamc_id`, `uamc_module`, `uamc_controller`, `uamc_virtual`, `uamc_activated`, `uamc_description`) VALUES
(1, 'noc', 'index', 0, 1, 'Default IndexController'),
(2, 'noc', 'auth', 0, 1, 'Authentication Controller'),
(3, 'noc', 'error', 0, 1, 'Error Controller'),
(4, 'noc', 'member', 0, 1, 'Member landing page after login'),
(5, 'admin', 'index', 0, 1, 'Admin Index Controller'),
(6, 'admin', 'action', 0, 1, 'Work on actions for a given controller'),
(7, 'admin', 'controller', 0, 1, 'Modify Controllers in the application'),
(8, 'admin', 'error', 0, 1, 'Error controller in the Admin Application'),
(9, 'admin', 'group', 0, 1, 'Application Group handling'),
(10, 'admin', 'role', 0, 1, 'Role handling'),
(11, 'admin', 'user', 0, 1, 'Application user handling'),
(12, 'webdesktop', 'index', 0, 1, 'Index Page for Webdesktop after Login'),
(13, 'webdesktop', 'api', 0, 1, 'General API Interface for Modules in the Webdesktop'),
(14, 'webdesktop', 'administration', 1, 1, 'Admin Module'),
(15, 'webdesktop', 'settings', 1, 1, 'User Settings Module');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_acl_roles`
--

CREATE TABLE IF NOT EXISTS `user_acl_roles` (
  `uar_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uar_name` varchar(64) NOT NULL,
  `uar_description` varchar(255) NOT NULL,
  `uar_activated` tinyint(2) unsigned NOT NULL,
  `uar_inherit` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uar_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `user_acl_roles`
--

INSERT INTO `user_acl_roles` (`uar_id`, `uar_name`, `uar_description`, `uar_activated`, `uar_inherit`) VALUES
(1, 'General User', 'Global General User Role, should have no rights', 1, 0),
(2, 'Active User', 'All Logged in Users', 1, 0),
(3, 'Admin', 'Global System Administrator', 1, 0),
(4, 'Demo role', 'Just a demo role', 1, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_acl_role_inherits`
--

CREATE TABLE IF NOT EXISTS `user_acl_role_inherits` (
  `uari_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uari_uar_id` int(10) unsigned NOT NULL,
  `uari_uar_inherit` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uari_id`),
  KEY `uari_uar_id` (`uari_uar_id`,`uari_uar_inherit`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Daten für Tabelle `user_acl_role_inherits`
--

INSERT INTO `user_acl_role_inherits` (`uari_id`, `uari_uar_id`, `uari_uar_inherit`) VALUES
(1, 2, 1),
(2, 3, 2),
(3, 4, 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_acl_role_member`
--

CREATE TABLE IF NOT EXISTS `user_acl_role_member` (
  `uarm_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uarm_uar_id` int(10) unsigned NOT NULL,
  `uarm_member_id` int(10) unsigned NOT NULL,
  `uarm_type` varchar(16) NOT NULL,
  PRIMARY KEY (`uarm_id`),
  KEY `uara_uar_id` (`uarm_uar_id`,`uarm_member_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Daten für Tabelle `user_acl_role_member`
--

INSERT INTO `user_acl_role_member` (`uarm_id`, `uarm_uar_id`, `uarm_member_id`, `uarm_type`) VALUES
(1, 3, 2, 'user'),
(2, 2, 2, 'group');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_acl_rules`
--

CREATE TABLE IF NOT EXISTS `user_acl_rules` (
  `uaru_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uaru_uamc_id` int(10) unsigned NOT NULL,
  `uaru_uaa_id` int(10) unsigned NOT NULL,
  `uaru_uar_id` int(10) unsigned NOT NULL,
  `uaru_rule` enum('allow','deny') NOT NULL,
  PRIMARY KEY (`uaru_id`),
  KEY `uaru_uamc_id` (`uaru_uamc_id`,`uaru_uaa_id`,`uaru_uar_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=73 ;

--
-- Daten für Tabelle `user_acl_rules`
--

INSERT INTO `user_acl_rules` (`uaru_id`, `uaru_uamc_id`, `uaru_uaa_id`, `uaru_uar_id`, `uaru_rule`) VALUES
(1, 1, 1, 1, 'allow'),
(2, 2, 2, 1, 'allow'),
(3, 2, 3, 1, 'allow'),
(4, 3, 4, 1, 'allow'),
(5, 4, 5, 2, 'allow'),
(6, 12, 35, 2, 'allow'),
(7, 13, 36, 2, 'allow'),
(8, 5, 6, 3, 'allow'),
(9, 6, 7, 3, 'allow'),
(10, 6, 8, 3, 'allow'),
(11, 6, 9, 3, 'allow'),
(12, 6, 10, 3, 'allow'),
(13, 6, 11, 3, 'allow'),
(14, 6, 12, 3, 'allow'),
(15, 6, 13, 3, 'allow'),
(16, 7, 14, 3, 'allow'),
(17, 7, 15, 3, 'allow'),
(18, 7, 16, 3, 'allow'),
(19, 7, 17, 3, 'allow'),
(20, 7, 18, 3, 'allow'),
(21, 7, 19, 3, 'allow'),
(22, 8, 20, 3, 'allow'),
(23, 9, 21, 3, 'allow'),
(24, 9, 22, 3, 'allow'),
(25, 9, 23, 3, 'allow'),
(26, 9, 24, 3, 'allow'),
(27, 10, 25, 3, 'allow'),
(28, 10, 26, 3, 'allow'),
(29, 10, 27, 3, 'allow'),
(30, 10, 28, 3, 'allow'),
(31, 10, 29, 3, 'allow'),
(32, 11, 30, 3, 'allow'),
(33, 11, 31, 3, 'allow'),
(34, 11, 32, 3, 'allow'),
(35, 11, 33, 3, 'allow'),
(36, 11, 34, 3, 'allow'),
(37, 14, 37, 3, 'allow'),
(38, 14, 38, 3, 'allow'),
(39, 14, 39, 3, 'allow'),
(40, 14, 40, 3, 'allow'),
(41, 14, 41, 3, 'allow'),
(42, 14, 42, 3, 'allow'),
(43, 14, 43, 3, 'allow'),
(44, 14, 44, 3, 'allow'),
(45, 14, 45, 3, 'allow'),
(46, 14, 46, 3, 'allow'),
(47, 14, 47, 3, 'allow'),
(48, 14, 48, 3, 'allow'),
(49, 14, 49, 3, 'allow'),
(50, 14, 50, 3, 'allow'),
(51, 14, 51, 3, 'allow'),
(52, 14, 52, 3, 'allow'),
(53, 14, 53, 3, 'allow'),
(54, 14, 54, 3, 'allow'),
(55, 14, 55, 3, 'allow'),
(56, 14, 56, 3, 'allow'),
(57, 14, 57, 3, 'allow'),
(58, 14, 58, 3, 'allow'),
(59, 14, 59, 3, 'allow'),
(60, 14, 60, 3, 'allow'),
(61, 14, 61, 3, 'allow'),
(62, 14, 62, 3, 'allow'),
(63, 14, 63, 3, 'allow'),
(64, 14, 64, 3, 'allow'),
(65, 15, 65, 2, 'allow'),
(66, 15, 66, 2, 'allow'),
(67, 15, 67, 2, 'allow'),
(68, 15, 68, 2, 'allow'),
(69, 15, 69, 2, 'allow'),
(70, 15, 70, 2, 'allow'),
(71, 15, 71, 2, 'allow'),
(72, 15, 72, 2, 'allow');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_groups`
--

CREATE TABLE IF NOT EXISTS `user_groups` (
  `ug_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `ug_name` varchar(32) NOT NULL,
  `ug_description` varchar(1024) NOT NULL,
  PRIMARY KEY (`ug_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Daten für Tabelle `user_groups`
--

INSERT INTO `user_groups` (`ug_id`, `ug_name`, `ug_description`) VALUES
(1, 'Default', 'A needed Default group for Guest User'),
(2, 'Group 1', 'A sample Group');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_launchers`
--

CREATE TABLE IF NOT EXISTS `user_launchers` (
  `l_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `l_m_id` int(10) unsigned NOT NULL,
  `l_u_id` int(10) unsigned NOT NULL,
  `l_type` tinyint(4) NOT NULL,
  PRIMARY KEY (`l_id`),
  KEY `l_m_id` (`l_m_id`,`l_u_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `user_launchers`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_styles`
--

CREATE TABLE IF NOT EXISTS `user_styles` (
  `us_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `us_uu_id` int(10) unsigned NOT NULL,
  `us_sth_id` tinyint(3) unsigned NOT NULL,
  `us_swp_id` int(10) unsigned NOT NULL,
  `us_backgroundcolor` varchar(6) NOT NULL,
  `us_fontcolor` varchar(6) NOT NULL,
  `us_transparency` tinyint(100) unsigned NOT NULL,
  `us_wallpaperpos` varchar(8) NOT NULL,
  PRIMARY KEY (`us_id`),
  KEY `us_uu_id` (`us_uu_id`),
  KEY `us_th_id` (`us_sth_id`,`us_swp_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Daten für Tabelle `user_styles`
--

INSERT INTO `user_styles` (`us_id`, `us_uu_id`, `us_sth_id`, `us_swp_id`, `us_backgroundcolor`, `us_fontcolor`, `us_transparency`, `us_wallpaperpos`) VALUES
(1, 1, 1, 1, '', '', 0, '0'),
(2, 2, 1, 5, '', '', 0, '0'),
(3, 3, 1, 1, '', '', 0, '0');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_users`
--

CREATE TABLE IF NOT EXISTS `user_users` (
  `uu_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uu_ug_id` smallint(5) unsigned NOT NULL,
  `uu_username` varchar(128) NOT NULL,
  `uu_passwort` varchar(32) NOT NULL,
  `uu_name` varchar(255) NOT NULL,
  `uu_email` varchar(255) NOT NULL,
  `uu_active` tinyint(4) NOT NULL DEFAULT '1',
  `uu_deleted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`uu_id`),
  KEY `uu_ug_id` (`uu_ug_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Daten für Tabelle `user_users`
--

INSERT INTO `user_users` (`uu_id`, `uu_ug_id`, `uu_username`, `uu_passwort`, `uu_name`, `uu_email`, `uu_active`, `uu_deleted`) VALUES
(1, 1, 'guest', 'no valid password', 'Default Guest User', 'guest@example.com', 1, '0000-00-00 00:00:00'),
(2, 2, 'admin', '', 'Admin User', 'admin@example.com', 1, '0000-00-00 00:00:00');

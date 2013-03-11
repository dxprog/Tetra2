-- phpMyAdmin SQL Dump
-- version 2.6.0-pl3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Apr 01, 2005 at 02:57 PM
-- Server version: 4.1.9
-- PHP Version: 4.3.10
-- 
-- Database: `tetradev`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `layout`
-- 

CREATE TABLE `layout` (
  `layout_id` int(4) NOT NULL auto_increment,
  `layout_position` int(2) NOT NULL default '0',
  `layout_type` int(2) NOT NULL default '0',
  `layout_data` varchar(20) NOT NULL default '',
  `layout_parent` int(4) NOT NULL default '0',
  `layout_user` int(10) NOT NULL default '0',
  PRIMARY KEY  (`layout_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `layout`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `mb_forums`
-- 

CREATE TABLE `mb_forums` (
  `forum_id` int(4) NOT NULL auto_increment,
  `forum_name` varchar(50) NOT NULL default '',
  `forum_description` varchar(255) NOT NULL default '',
  `forum_postperms` int(2) NOT NULL default '0',
  `forum_viewperms` int(2) NOT NULL default '0',
  `forum_parent` int(4) NOT NULL default '0',
  `forum_news` tinyint(1) NOT NULL default '0',
  `forum_topicperms` int(2) NOT NULL default '0',
  PRIMARY KEY  (`forum_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `mb_forums`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `mb_messages`
-- 

CREATE TABLE `mb_messages` (
  `message_id` bigint(20) NOT NULL auto_increment,
  `message_poster` int(10) NOT NULL default '0',
  `message_body` text NOT NULL,
  `message_parent` int(10) NOT NULL default '0',
  `message_attachment` varchar(255) NOT NULL default '',
  `message_date` int(10) NOT NULL default '0',
  `message_edits` int(2) NOT NULL default '0',
  PRIMARY KEY  (`message_id`),
  FULLTEXT KEY `mb_index` (`message_body`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `mb_read`
-- 

CREATE TABLE `mb_read` (
  `read_id` bigint(32) NOT NULL auto_increment,
  `read_user` int(10) NOT NULL default '0',
  `read_topic` int(10) NOT NULL default '0',
  `read_message` bigint(20) NOT NULL default '0',
  `read_flagged` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`read_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `mb_read`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `mb_topics`
-- 

CREATE TABLE `mb_topics` (
  `topic_id` int(10) NOT NULL auto_increment,
  `topic_title` varchar(100) NOT NULL default '',
  `topic_firstpost` bigint(20) NOT NULL default '0',
  `topic_lastpost` bigint(20) NOT NULL default '0',
  `topic_numposts` int(10) NOT NULL default '0',
  `topic_to` int(10) NOT NULL default '0',
  `topic_parent` int(4) NOT NULL default '0',
  `topic_locked` tinyint(1) NOT NULL default '0',
  `topic_sticky` tinyint(1) NOT NULL default '0',
  `topic_from` int(10) NOT NULL default '0',
  PRIMARY KEY  (`topic_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `mb_topics`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `modules`
-- 

CREATE TABLE `modules` (
  `module_id` int(3) NOT NULL auto_increment,
  `module_name` varchar(40) NOT NULL default '',
  `module_api` varchar(10) NOT NULL default '',
  `module_parent` int(3) NOT NULL default '0',
  `module_class` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`module_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `modules`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `navigation`
-- 

CREATE TABLE `navigation` (
  `nav_id` int(4) NOT NULL auto_increment,
  `nav_href` varchar(255) NOT NULL default '',
  `nav_caption` varchar(50) NOT NULL default '',
  `nav_type` int(2) NOT NULL default '0',
  PRIMARY KEY  (`nav_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `navigation`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `poll_votes`
-- 

CREATE TABLE `poll_votes` (
  `vote_id` bigint(20) NOT NULL auto_increment,
  `vote_caption` varchar(100) NOT NULL default '',
  `vote_parent` int(10) NOT NULL default '0',
  `vote_count` int(10) NOT NULL default '0',
  PRIMARY KEY  (`vote_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `poll_votes`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `polls`
-- 

CREATE TABLE `polls` (
  `poll_id` int(10) NOT NULL auto_increment,
  `poll_question` varchar(150) NOT NULL default '',
  `poll_date` int(10) NOT NULL default '0',
  PRIMARY KEY  (`poll_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `polls`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `sessions`
-- 

CREATE TABLE `sessions` (
  `sess_key` char(32) NOT NULL default '',
  `sess_uid` int(10) NOT NULL default '0',
  `sess_expires` int(15) NOT NULL default '0',
  PRIMARY KEY  (`sess_key`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `sessions`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `users`
-- 

CREATE TABLE `users` (
  `user_id` int(10) NOT NULL auto_increment,
  `user_name` varchar(32) NOT NULL default '',
  `user_pass` varchar(32) NOT NULL default '',
  `user_email` varchar(50) NOT NULL default '',
  `user_rname` varchar(100) NOT NULL default '',
  `user_joined` int(10) NOT NULL default '0',
  `user_bdate` int(10) NOT NULL default '0',
  `user_zone` int(4) NOT NULL default '0',
  `user_lastlogin` int(10) NOT NULL default '0',
  `user_showemail` tinyint(1) NOT NULL default '0',
  `user_theme` varchar(15) NOT NULL default '',
  `user_style` text NOT NULL,
  `user_msn` varchar(50) NOT NULL default '',
  `user_aim` varchar(30) NOT NULL default '',
  `user_icq` varchar(10) NOT NULL default '',
  `user_irc` varchar(20) NOT NULL default '',
  `user_http` text NOT NULL,
  `user_rank` tinyint(1) NOT NULL default '0',
  `user_activated` tinyint(1) NOT NULL default '1',
  `user_actid` varchar(32) NOT NULL default '',
  `user_banned` tinyint(1) NOT NULL default '0',
  `user_from` varchar(30) NOT NULL default '',
  `user_ip` varchar(255) NOT NULL default '',
  `user_tf` varchar(15) NOT NULL default 'Y-m-d H:i:s',
  `user_cookielife` tinyint(1) NOT NULL default '0',
  `user_mb_signature` varchar(255) NOT NULL default '',
  `user_mb_avatar` varchar(255) NOT NULL default './templates/generic/styles/images/tetra_avatar.gif',
  `user_mb_title` varchar(25) NOT NULL default 'n00b',
  `user_mb_smileys` tinyint(1) NOT NULL default '1',
  `user_mb_tpp` int(2) NOT NULL default '30',
  `user_mb_mpp` int(2) NOT NULL default '15',
  `user_poll_voted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `users`
-- 
INSERT INTO `users` VALUES (1, 'Guest', '', '', 'Guest', 0, 0, 0, 0, 0, 'Tetra', 'Purple', '', '', '', '', '', 0, 0, '', 0, '', '0.0.0.0', 'Y-m-d H:i:s', 0, '', './templates/generic/styles/images/tetra_avatar.gif', 'n00b', 1, 30, 15, 0);
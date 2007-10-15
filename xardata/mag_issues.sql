-- phpMyAdmin SQL Dump
-- version 2.9.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Oct 15, 2007 at 12:16 PM
-- Server version: 4.1.20
-- PHP Version: 4.3.9
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `xar_mag_issues`
-- 

CREATE TABLE `xar_mag_issues` (
  `iid` int(11) NOT NULL auto_increment,
  `mag_id` int(11) NOT NULL default '0',
  `ref` varchar(60) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `number` int(11) default NULL,
  `status` varchar(30) NOT NULL default 'DRAFT',
  `pubdate` int(11) NOT NULL default '0',
  `tagline` varchar(255) default NULL,
  `cover_img` varchar(255) default NULL,
  `abstract` text,
  `premium` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`iid`),
  KEY `mag_id` (`mag_id`),
  KEY `ref` (`ref`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

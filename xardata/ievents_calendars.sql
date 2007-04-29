-- phpMyAdmin SQL Dump
-- version 2.9.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Apr 28, 2007 at 07:17 PM
-- Server version: 4.1.20
-- PHP Version: 4.3.9
-- 
-- Database: `xaraya1`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `xar_ievents_calendars`
-- 

CREATE TABLE `xar_ievents_calendars` (
  `cid` int(11) NOT NULL auto_increment,
  `status` varchar(10) NOT NULL default 'ACTIVE',
  `short_name` varchar(60) NOT NULL default '',
  `long_name` varchar(200) default NULL,
  `description` text,
  PRIMARY KEY  (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `xar_ievents_calendars`
-- 

INSERT INTO `xar_ievents_calendars` (`cid`, `status`, `short_name`, `long_name`, `description`) VALUES (1, 'ACTIVE', 'First Calendar', 'First Calendar', 'Some longer description, probably in HTML format.');

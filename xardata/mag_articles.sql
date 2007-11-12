-- phpMyAdmin SQL Dump
-- version 2.9.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Oct 15, 2007 at 12:19 PM
-- Server version: 4.1.20
-- PHP Version: 4.3.9
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `xar_mag_articles`
-- 

CREATE TABLE `xar_mag_articles` (
  `aid` int(11) NOT NULL auto_increment,
  `issue_id` int(11) NOT NULL default '0',
  `series_id` int(11) NOT NULL default '0',
  `ref` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `subtitle` varchar(255) default NULL,
  `summary` text,
  `body` text NOT NULL,
  `footer` text,
  `status` varchar(30) NOT NULL default 'DRAFT',
  `pubdate` int(11) NOT NULL default '0',
  `refs` text,
  `page` int(11) default NULL,
  `tags` varchar(255) default NULL,
  `premium` varchar(30) NOT NULL default '',
  `style` varchar(30) NOT NULL default 'MAIN',
  `image1` varchar(255) default NULL,
  `image1_alt` varchar(255) NOT NULL default '',
  `hitcount` int(11) NOT NULL default 0,
  PRIMARY KEY  (`aid`),
  KEY `issue_id` (`issue_id`),
  KEY `series_id` (`series_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=48 ;

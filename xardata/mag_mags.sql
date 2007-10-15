-- phpMyAdmin SQL Dump
-- version 2.9.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Oct 15, 2007 at 12:14 PM
-- Server version: 4.1.20
-- PHP Version: 4.3.9
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `xar_mag_mags`
-- 

CREATE TABLE `xar_mag_mags` (
  `mid` int(11) NOT NULL auto_increment,
  `ref` varchar(60) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `showin` varchar(30) NOT NULL default '',
  `subtitle` varchar(255) default NULL,
  `status` varchar(30) NOT NULL default 'ACTIVE',
  `synopsis` text,
  `logo` varchar(255) default NULL,
  `premium` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`mid`),
  UNIQUE KEY `ref` (`ref`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

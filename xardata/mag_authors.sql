-- phpMyAdmin SQL Dump
-- version 2.9.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Oct 15, 2007 at 12:20 PM
-- Server version: 4.1.20
-- PHP Version: 4.3.9
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `xar_mag_authors`
-- 

CREATE TABLE `xar_mag_authors` (
  `auid` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `mini_bio` text,
  `full_bio` text,
  `photo` varchar(255) default NULL,
  `email` varchar(255) default NULL,
  `website` varchar(255) default NULL,
  `contact` text,
  `notes` text,
  PRIMARY KEY  (`auid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

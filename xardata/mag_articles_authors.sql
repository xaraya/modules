-- phpMyAdmin SQL Dump
-- version 2.9.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Oct 15, 2007 at 12:21 PM
-- Server version: 4.1.20
-- PHP Version: 4.3.9
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `xar_mag_articles_authors`
-- 

CREATE TABLE `xar_mag_articles_authors` (
  `aaid` int(11) NOT NULL auto_increment,
  `article_id` int(11) NOT NULL default '0',
  `author_id` int(11) NOT NULL default '0',
  `role` varchar(30) NOT NULL default 'WRITER',
  `notes` text,
  PRIMARY KEY  (`aaid`),
  KEY `article_id` (`article_id`),
  KEY `author_id` (`author_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;


/* Until we have an initialisation and upgrade script, create the link tracker table by hand. */

CREATE TABLE `xar_jquery_linktracker` (
  `id` int(11) NOT NULL auto_increment,
  `page` varchar(250) NOT NULL default '',
  `page_host` varchar(250) NOT NULL default '',
  `page_path` varchar(250) NOT NULL default '',
  `page_query` varchar(250) NOT NULL default '',
  `link_id` varchar(250) NOT NULL default '',
  `target` varchar(250) NOT NULL default '',
  `target_host` varchar(250) NOT NULL default '',
  `target_path` varchar(250) NOT NULL default '',
  `target_query` varchar(250) NOT NULL default '',
  `label` varchar(250) NOT NULL default '',
  `utimestamp` int(11) NOT NULL default '0',
  `year` int(4) NOT NULL default '0',
  `month` int(2) NOT NULL default '0',
  `day` int(2) NOT NULL default '0',
  `ip_address` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`id`)
);

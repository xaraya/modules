<?php
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2003 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Jim McDonald
// Modified by: Nuncanada
// Modified by: marcinmilan
// Purpose of file:  Initialisation functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003 XT-Commerce
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------

  require_once 'includes/classes/carp.php';
CarpConf('linkdiv', '<div class="dataTableHeadingRow"">');
CarpConf('linkstyle', 'text-decoration:none');
CarpConf('linkclass', 'newslink');
CarpConf('showdesc|showctitle|showcdesc', 1);
CarpConf('maxitems', 5);
CarpConf('cclass', 'h2');
CarpConf('postitem','');
CarpConf('poweredby','');
CarpShow('http://www.xt-commerce.com/backend.php', 'xt-news.cache');
CarpConfReset();


CarpConf('linkdiv','<div style="background:#cccccc; width:185; padding:2px; border-width:1px; border-style:solid; border-color:#333333;">');
CarpConf('linkstyle','text-decoration:none');
CarpConf('linkclass','h3');
CarpConf('showdesc|showctitle|showcdesc',1);
CarpConf('maxitems',10);
CarpConf('cclass','h2');
CarpConf('postitems','');
CarpConf('poweredby','');
//CarpShow("http://www.xt-commerce.com/modules/xp_syndication/mods/mylinks_rss.php","xt-links.cache");
CarpConfReset();
}
?>
<?php
/* -----------------------------------------------------------------------------------------
   $Id: column_left.php,v 1.2 2003/09/07 10:38:25 fanta2k Exp $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(column_left.php,v 1.14 2003/02/10); www.oscommerce.com 
   (c) 2003	 nextcommerce (column_left.php,v 1.7 2003/08/21); www.nextcommerce.org 

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

  if ( (USE_CACHE == 'true') && !defined('SID')) {
    echo xtc_cache_categories_box();
  } else {
    include(DIR_WS_BOXES . 'categories.php');
  }

  if ( (USE_CACHE == 'true') && !defined('SID')) {
    echo xtc_cache_manufacturers_box();
  } else {
    include(DIR_WS_BOXES . 'manufacturers.php');
  }
  require(DIR_WS_BOXES . 'add_a_quickie.php');
  require(DIR_WS_BOXES . 'whats_new.php');
  require(DIR_WS_BOXES . 'search.php');
  require(DIR_WS_BOXES . 'content.php');
  require(DIR_WS_BOXES . 'information.php');
?>
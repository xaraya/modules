<?php
// ----------------------------------------------------------------------
// Copyright (C) 2004: Marc Lutolf (marcinmilan@xaraya.com)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003 XT-Commerce
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------

//! Cache the manufacturers box
// Cache the manufacturers box
  function commerce_userapi_cache_manufacturers_box($auto_expire = false, $refresh = false) {

    if (($refresh == true) || !read_cache($cache_output, 'manufacturers_box-' . $_SESSION['language'] . '.cache' . $_GET['manufacturers_id'], $auto_expire)) {
      ob_start();
      include(DIR_WS_BOXES . 'manufacturers.php');
      $cache_output = ob_get_contents();
      ob_end_clean();
      write_cache($cache_output, 'manufacturers_box-' . $_SESSION['language'] . '.cache' . $_GET['manufacturers_id']);
    }

    return $cache_output;
  }
 ?>
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

// Auto activate banners
  function commerce_userapi_activate_banners() {
    $banners_query = new xenQuery("select banners_id, date_scheduled from " . TABLE_BANNERS . " where date_scheduled != ''");
    $banners_query->run();
    if ($banners_query->getrows($banners_query)) {
      $q = new xenQuery();
      if(!$q->run()) return;
      while ($banners = $q->output()) {
        if (date('Y-m-d H:i:s') >= $banners['date_scheduled']) {
          xtc_set_banner_status($banners['banners_id'], '1');
        }
      }
    }
  }
 ?>
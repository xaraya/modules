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

  function commerce_userapi_get_zone_name($country_id, $zone_id, $default_zone) {
    $zone_query = new xenQuery("select zone_name from " . TABLE_ZONES . " where zone_country_id = '" . $country_id . "' and zone_id = '" . $zone_id . "'");
    if ($zone_query->getrows()) {
      $q = new xenQuery();
      $q->run();
      $zone = $q->output();
      return $zone['zone_name'];
    } else {
      return $default_zone;
    }
  }
 ?>
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

  function commerce_userapi_get_products_stock($products_id) {
    $products_id = xtc_get_prid($products_id);
    $stock_query = new xenQuery("select products_quantity from " . TABLE_PRODUCTS . " where products_id = '" . $products_id . "'");
      $q = new xenQuery();
      $q->run();
    $stock_values = $q->output();

    return $stock_values['products_quantity'];
  }

 ?>
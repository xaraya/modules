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

  function commerce_userapi_count_products_in_category($category_id, $include_inactive = false) {
    $products_count = 0;
    if ($include_inactive == true) {
      $products_query = new xenQuery("select count(*) as total from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = p2c.products_id and p2c.categories_id = '" . $category_id . "'");
    } else {
      $products_query = new xenQuery("select count(*) as total from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = p2c.products_id and p.products_status = '1' and p2c.categories_id = '" . $category_id . "'");
    }
      $q = new xenQuery();
      $q->run();
    $products = $q->output();
    $products_count += $products['total'];

    $child_categories_query = new xenQuery("select categories_id from " . TABLE_CATEGORIES . " where parent_id = '" . $category_id . "'");
    if ($child_categories_query->getrows()) {
      $q = new xenQuery();
      $q->run();
      while ($child_categories = $q->output()) {
        $products_count += xtc_count_products_in_category($child_categories['categories_id'], $include_inactive);
      }
    }

    return $products_count;
  }
 ?>
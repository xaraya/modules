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

  function commerce_userapi_has_category_subcategories($category_id) {
    $child_category_query = new xenQuery("select count(*) as count from " . TABLE_CATEGORIES . " where parent_id = '" . $category_id . "'");
      $q = new xenQuery();
      $q->run();
    $child_category = $q->output();

    if ($child_category['count'] > 0) {
      return true;
    } else {
      return false;
    }
  }

 ?>
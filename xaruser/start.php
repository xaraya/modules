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

  function commerce_user_start()
  {
//      include( 'includes/application_top.php');
      // the following cPath references come from application_top.php
      $category_depth = 'top';
      if (isset($cPath) && xarModAPIFunc('commerce','user','not_null',array('arg' => $cPath))) {
        $categories_products_query = new xenQuery("select count(*) as total from " . TABLE_PRODUCTS_TO_CATEGORIES . " where categories_id = '" . $current_category_id . "'");
      $q = new xenQuery();
      $q->run();
        $cateqories_products = $q->output();
        if ($cateqories_products['total'] > 0) {
          $category_depth = 'products'; // display products
        } else {
          $category_parent_query = new xenQuery("select count(*) as total from " . TABLE_CATEGORIES . " where parent_id = '" . $current_category_id . "'");
      $q = new xenQuery();
      $q->run();
          $category_parent = $q->output();
          if ($category_parent['total'] > 0) {
            $category_depth = 'nested'; // navigate through the categories
          } else {
            $category_depth = 'products'; // category has no products, but display the 'no products' message
          }
        }
      }


//  include ('modules/commerce/includes/modules/default.php');
echo "dd";exit;

  $data = array();
  return $data;
}
?>
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

//TODO
function commerce_userapi_get_path($current_category_id = '') {
    global $cPath_array;

    if (xarModAPIFunc('commerce','user','not_null',array('arg' => $current_category_id))) {
      $cp_size = sizeof($cPath_array);
      if ($cp_size == 0) {
        $cPath_new = $current_category_id;
      } else {
        $cPath_new = '';
        $last_category_query = new xenQuery("select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . $cPath_array[($cp_size-1)] . "'");
      $q = new xenQuery();
      $q->run();
        $last_category = $q->output();

        $current_category_query = new xenQuery("select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . $current_category_id . "'");
      $q = new xenQuery();
      $q->run();
        $current_category = $q->output();

        if ($last_category['parent_id'] == $current_category['parent_id']) {
          for ($i=0; $i<($cp_size-1); $i++) {
            $cPath_new .= '_' . $cPath_array[$i];
          }
        } else {
          for ($i=0; $i<$cp_size; $i++) {
            $cPath_new .= '_' . $cPath_array[$i];
          }
        }
        $cPath_new .= '_' . $current_category_id;

        if (substr($cPath_new, 0, 1) == '_') {
          $cPath_new = substr($cPath_new, 1);
        }
      }
    } else {
      $cPath_new = implode('_', $cPath_array);
    }

    return 'cPath=' . $cPath_new;
  }
 ?>
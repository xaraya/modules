<?php
// ----------------------------------------------------------------------
// Copyright (C) 2004: Marc Lutolf (marcinmilan@xaraya.com)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003 XT-Commerce
//   Third Party contributions:
//   New Attribute Manager v4b                Autor: Mike G | mp3man@internetwork.net | http://downloads.ephing.com
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------

  function commerce_userapi_findTitle($current_pid, $languageFilter) {
    $query = "SELECT * FROM products_description where language_id = '" . $_SESSION['languages_id'] . "' AND products_id = '" . $current_pid . "'";

    $result = mysql_query($query) or die(mysql_error());

    $matches = mysql_num_rows($result);

    if ($matches) {
      while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $productName = $line['products_name'];
      }
      return $productName;
    } else {
      return "Something isn't right....";
    }
  }
?>
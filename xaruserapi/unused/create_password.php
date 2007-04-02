<?php
// ----------------------------------------------------------------------
// Copyright (C) 2004: Marc Lutolf (marcinmilan@xaraya.com)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003 XT-Commerce
// ----------------------------------------------------------------------

  function commerce_userapi_create_password($length) {
    if ($length > 36) {
      return "ERROR";
    } else {
      $str = md5(time());
      $cutoff = 31 - $length;
      $start = rand(0, $cutoff);
      return substr($str, $start, $length);
    }
  }

  ?>

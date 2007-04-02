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

function commerce_adminapi_create_password($args) {
    extract($args);
    if(!isset($length)) $length = 8;
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
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


  function commerce_userapi_write_user_info($customer_id, $user_info) {
//    global $customer_id, $user_info;                                                                                                                                             customers_id,           customers_ip,               customers_ip_date,  customers_host,                      customers_advertiser,                customers_referer_url
    xtc_db_query("insert into " . TABLE_CUSTOMERS_IP . " (customers_id, customers_ip, customers_ip_date, customers_host, customers_advertiser, customers_referer_url) values ('" . $customer_id . "', '" . $user_info['user_ip'] . "', now(), '" . $user_info['user_host'] . "', '" . $user_info['advertiser'] . "',  '" . $user_info['referer_url'] . "')");
    return -1;
  }
?>
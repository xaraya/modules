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

function commerce_adminapi_get_user_info($args) {
    sys::import('modules.xen.xarclasses.xenquery');
    xarModAPILoad('commerce');
    $xartables = xarDBGetTables();
    extract($args);
    $q = new xenQuery('SELECT',$xartables['commerce_customers_ip']);
    $q->addfields(array('customers_ip', 'customers_ip_date', 'customers_host', 'customers_advertiser', 'customers_referer_url'));
    $q->eq('customers_id',$customer_id);
    if(!$q->run()) return;
    return $q->output();
}
?>
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

function commerce_userapi_get_customer_status($args) {
    include_once 'modules/xen/xarclasses/xenquery.php';
    xarModAPILoad('commerce');
    $xartables = xarDBGetTables();

    extract($args);
    $q = new xenQuery('SELECT',
                      $xartables['commerce_customers_status'],
                      array('customers_status_id', 'customers_status_name', 'customers_status_public', 'customers_status_image', 'customers_status_discount', 'customers_status_ot_discount_flag', 'customers_status_ot_discount', 'customers_status_graduated_prices')
                     );
    if (!isset($language_id)) {
        $localeinfo = xarLocaleGetInfo(xarMLSGetSiteLocale());
        $locale = $localeinfo['lang'] . "_" . $localeinfo['country'];
        $language_id = $language['id'];
    }
    $q->eq('language_id',$language_id);
    if (isset($customer_id)) {
        $q->addtable($xartables['commerce_customers']);
        $q->join('customers_status','customers_status_id');
        $q->eq('customers_id',$customer_id);
    }
    else if (isset($customer_status_id)) {
        $q->eq('customers_status_id',$customer_status_id);
    }
    $q->run();
    return $q->row();
}
?>
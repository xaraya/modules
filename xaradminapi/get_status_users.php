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

function commerce_adminapi_get_status_users($args) {
    sys::import('modules.xen.xarclasses.xenquery');
    xarModAPILoad('commerce');
    $xartables = xarDB::getTables();
    extract($args);
    if(!isset($status_id)) $status_id = 0;
    $q = new xenQuery('SELECT',$xartables['commerce_customers']);
    $q->addfields(array('count(customers_status) as count'));
    $q->eq('customers_status',$status_id);
    if(!$q->run()) return;
    $status_data = $q->row();
    return $status_data['count'];
}
?>
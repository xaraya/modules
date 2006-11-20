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

function commerce_adminapi_get_group_price($args) {
    sys::import('modules.xen.xarclasses.xenquery');
    xarModAPILoad('commerce');
    $xartables = xarDBGetTables();
    extract($args);

    // well, first try to get group price from database
    $q = new xenQuery('SELECT',"personal_offers_by_customers_status_" . $group_id,'personal_offer');
    $q->eq('products_id',$product_id);
    if(!$q->run()) return;
    $group_price_data = $q->row();

    // if we found a price, everything is ok if not, we will create new entry
    if ($group_price_data['personal_offer'] == '') {
        $q = new xenQuery('INSERT',"personal_offers_by_customers_status_" . $group_id,'personal_offer');
        $q->addfield(price_id,'');
        $q->addfield(products_id,$product_id);
        $q->addfield(quantity,1);
        $q->addfield(personal_offer,0.00);
        if(!$q->run()) return;
        $group_price_data = $q->row();
    }
    return $group_price_data['personal_offer'];
}
?>
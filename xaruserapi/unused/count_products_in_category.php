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

function commerce_userapi_count_products_in_category($args)
{
    sys::import('modules.xen.xarclasses.xenquery');
    $xartables = xarDB::getTables();

    extract($args);
    if (!isset($include_inactive)) $include_inactive = false;
    $products_count = 0;
    $q = new xenQuery('SELECT');
    $q->addtable($xartables['commerce_products'], 'p');
    $q->addtable($xartables['commerce_products_to_categories'], 'p2c');
    $q->addfield('count(*) as count');
    $q->join('p.products_id','p2c.products_id');
    $q->eq('p2c.categories_id',$cid);
    if ($include_inactive == false) {
        $q->eq('p.products_status',1);
    }
    if(!$q->run()) return;
    $products = $q->row();
    $products_count += $products['count'];

    $q = new xenQuery('SELECT', $xartables['categories'], 'xar_cid as cid');
    $q->eq('xar_parent',$cid);
    if(!$q->run()) return;
    foreach ($q->output() as $child_categories) {
        $products_count += xarModAPIFunc('commerce','user','count_products_in_category', array('cid' => $child_categories['cid'], 'include_active' => $include_inactive));
    }
    return $products_count;
}
 ?>
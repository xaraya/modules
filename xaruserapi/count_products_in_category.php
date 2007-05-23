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

function products_userapi_count_product_in_category($args)
{
    sys::import('modules.xen.xarclasses.xenquery');
    $xartables = xarDB::getTables();

    extract($args);
    if (!isset($include_inactive)) $include_inactive = false;
    $product_count = 0;
    $q = new xenQuery('SELECT');
    $q->addtable($xartables['product_products'], 'p');
    $q->addtable($xartables['product_product_to_categories'], 'p2c');
    $q->addfield('count(*) as count');
    $q->join('p.product_id','p2c.product_id');
    if (isset($cid)) {
        $q->eq('p2c.categories_id',$cid);
    }
    if ($include_inactive == false) {
        $q->eq('p.product_status',1);
    }
    if(!$q->run()) return;
    $products = $q->row();
    $product_count += $products['count'];

    if (isset($cid)) {
        $q = new xenQuery('SELECT', $xartables['categories'], 'id as cid');
        $q->eq('parent_id',$cid);
        if(!$q->run()) return;
        foreach ($q->output() as $child_categories) {
            $product_count += xarModAPIFunc('products','user','count_product_in_category', array('cid' => $child_categories['cid'], 'include_active' => $include_inactive));
        }
    }
    return $product_count;
}
 ?>
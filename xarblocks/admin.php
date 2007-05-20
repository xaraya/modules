<?php
// ----------------------------------------------------------------------
// Copyright (C) 2004: Marc Lutolf (marcinmilan@xaraya.com)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003-4 Xaraya
//  (c) 2003 XT-Commerce
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------

/**
 * Initialise the block
 */
function commerce_adminblock_init()
{
    return array(
        'content_text' => '',
        'content_type' => 'text',
        'expire' => 0,
        'hide_empty' => true,
        'custom_format' => '',
        'hide_errors' => true,
        'start_date' => '',
        'end_date' => ''
    );
}

/**
 * Get information on the block ($blockinfo array)
 */
function commerce_adminblock_info()
{
    return array(
        'text_type' => 'Content',
        'text_type_long' => 'Generic Content Block',
        'module' => 'commerce',
        'func_update' => '',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true,
        'notes' => "content_type can be 'text', 'html', 'php' or 'data'"
    );
}

/**
 * Display function
 * @param $blockinfo array
 * @returns $blockinfo array
 */
function commerce_adminblock_display($blockinfo)
{
    // Security Check
    if (!xarSecurityCheck('ViewCommerceBlocks', 0, 'Block', "content:$blockinfo[title]:All")) {return;}

    sys::import('modules.xen.xarclasses.xenquery');
    xarModAPILoad('commerce');
    $xartables = xarDB::getTables();

    $localeinfo = xarLocaleGetInfo(xarMLSGetSiteLocale());
    $data['language'] = $localeinfo['lang'] . "_" . $localeinfo['country'];
    $currentlang = xarModAPIFunc('commerce','user','get_language',array('locale' => $data['language']));
    $language_id = $currentlang['id'];

$box_content='';

    if(!xarVarFetch('cPath',  'int',  $data['cPath'], 0, XARVAR_NOT_REQUIRED)) {return;}
    $orders_contents = '';

    $q = new xenQuery('SELECT',$xartables['commerce_orders_status'],array('orders_status_name', 'orders_status_id'));
    $q->eq('language_id',$language_id);
    if(!$q->run()) return;
    $order_status = array();
    foreach ($q->output() as $orders) {
        $q = new xenQuery('SELECT',$xartables['commerce_orders'],'count(*) AS count');
        $q->eq('orders_status',$orders['orders_status_id']);
        if(!$q->run()) return;
        $orders_pending = $q->row();
        $row['name'] = $orders['orders_status_name'];
        $row['id'] = $orders['orders_status_id'];
        $row['count'] = $orders_pending['count'];
        $order_status[] = $row;
    }
    $data['order_status'] = $order_status;

    $q = new xenQuery('SELECT',$xartables['commerce_customers'],'count(*) AS count');
    if(!$q->run()) return;
    $data['customers'] = $q->row();

    if (xarModIsAvailable('products')) {
        $data['products'] = xarModAPIFunc('products','user','count_products_in_category');
    }

    $q = new xenQuery('SELECT',$xartables['commerce_reviews'],'count(*) AS count');
    if(!$q->run()) return;
    $data['reviews'] = $q->row();

    $box_content= $data;
    $blockinfo['content'] = $box_content;
    return $blockinfo;
}
?>
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

  function commerce_user_start()
  {
    include_once 'modules/xen/xarclasses/xenquery.php';
    $xartables = xarDBGetTables();

//      include( 'includes/application_top.php');
      // the following cPath references come from application_top.php
      $category_depth = 'top';
    if (isset($cPath) && xarModAPIFunc('commerce','user','not_null',array('arg' => $cPath))) {
//        $categories_products_query = new xenQuery("SELECT count(*) as total from " . TABLE_PRODUCTS_TO_CATEGORIES . " where categories_id = '" . $current_category_id . "'");
        $q = new xenQuery('SELECT',$xartables['commerce_products_to_categories']);
        $q->addfield('count(*) AS total');
        $q->eq('categories_id',$current_category_id);
        echo "SELECT count(*) as total from " . $xartables['commerce_products_to_categories'] . " where categories_id = '" . $current_category_id . "'<br>";
        $q->qecho();exit;
        if(!$q->run()) return;
        $cateqories_products = $q->output();
        if ($cateqories_products['total'] > 0) {
            $category_depth = 'products'; // display products
        }
        else {
            $category_parent_query = new xenQuery("select count(*) as total from " . TABLE_CATEGORIES . " where parent_id = '" . $current_category_id . "'");
            $q = new xenQuery('SELECT',$xartables['commerce_categories']);
            $q->addfield('count(*) AS total');
            $q->eq('parent_id',$current_category_id);
            if(!$q->run()) return;
            $category_parent = $q->output();
            if ($category_parent['total'] > 0) {
                $category_depth = 'nested'; // navigate through the categories
            }
            else {
                $category_depth = 'products'; // category has no products, but display the 'no products' message
            }
        }
    }


    include ('modules/commerce/xarincludes/modules/default.php');


    // Show the categories block
    $blockinfo = xarModAPIFunc('blocks', 'user', 'get', array('name'=> 'commercecategories'));
    if(!xarModAPIFunc('blocks', 'admin', 'activate', array('bid' => $blockinfo['bid']))) return;

    // Show the admin info block
    $blockinfo = xarModAPIFunc('blocks', 'user', 'get', array('name'=> 'commerceadmininfo'));
    if(!xarModAPIFunc('blocks', 'admin', 'activate', array('bid' => $blockinfo['bid']))) return;

    // Show the search block
    $blockinfo = xarModAPIFunc('blocks', 'user', 'get', array('name'=> 'commercesearch'));
    if(!xarModAPIFunc('blocks', 'admin', 'activate', array('bid' => $blockinfo['bid']))) return;

    // Show the information block
    $blockinfo = xarModAPIFunc('blocks', 'user', 'get', array('name'=> 'commerceinformation'));
    if(!xarModAPIFunc('blocks', 'admin', 'activate', array('bid' => $blockinfo['bid']))) return;

    // Show the language block
    $blockinfo = xarModAPIFunc('blocks', 'user', 'get', array('name'=> 'commercelanguage'));
    if(!xarModAPIFunc('blocks', 'admin', 'activate', array('bid' => $blockinfo['bid']))) return;

    // Show the manufacturers block
    $blockinfo = xarModAPIFunc('blocks', 'user', 'get', array('name'=> 'commercemanufacturers'));
    if(!xarModAPIFunc('blocks', 'admin', 'activate', array('bid' => $blockinfo['bid']))) return;

    // Show the currencies block
    $blockinfo = xarModAPIFunc('blocks', 'user', 'get', array('name'=> 'commercecurrencies'));
    if(!xarModAPIFunc('blocks', 'admin', 'activate', array('bid' => $blockinfo['bid']))) return;

    // Show the shopping cart block
    $blockinfo = xarModAPIFunc('blocks', 'user', 'get', array('name'=> 'commercecart'));
    if(!xarModAPIFunc('blocks', 'admin', 'activate', array('bid' => $blockinfo['bid']))) return;

    // Show  the exit menu
    $blockinfo = xarModAPIFunc('blocks', 'user', 'get', array('name'=> 'commerceexit'));
    if(!xarModAPIFunc('blocks', 'admin', 'activate', array('bid' => $blockinfo['bid']))) return;

//    $data['account'] = xarModAPIFunc('commerce','user','getaccount', array('accountid' => $account));
    $data['account'] = 1;
    return $data;
}
?>
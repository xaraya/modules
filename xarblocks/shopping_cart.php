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
function commerce_shopping_cartblock_init()
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
function commerce_shopping_cartblock_info()
{
    return array(
        'text_type' => 'Content',
        'text_type_long' => 'Generic Content Block',
        'module' => 'commerce',
        'func_update' => 'commerce_shopping_cart_update',
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
function commerce_shopping_cartblock_display($blockinfo)
{
    // Security Check
    if (!xarSecurityCheck('ViewCommerceBlocks', 0, 'Block', "content:$blockinfo[title]:All")) {return;}


//$box_content='';
$box_price_string='';
  // include needed files
  require_once(DIR_FS_INC . 'xtc_format_price.inc.php');
  require_once(DIR_FS_INC . 'xtc_draw_separator.inc.php');
  require_once(DIR_FS_INC . 'xtc_recalculate_price.inc.php');



  if ($_SESSION['cart']->count_contents() > 0) {
    $products = $_SESSION['cart']->get_products();
    $products_in_cart=array();
    $qty=0;
    for ($i=0, $n=sizeof($products); $i<$n; $i++) {
    $qty+=$products[$i]['quantity'];
      $products_in_cart[]=array(
                                'QTY'=>$products[$i]['quantity'],
                                'LINK'=>xarModURL('commerce','user','product_info',array('products_id' => $products[$i]['id'])),
                                'NAME'=>$products[$i]['name']);

    }
  $box_smarty->assign('PRODUCTS',$qty);
  $box_smarty->assign('empty','false');
  } else {
  // cart empty
  $box_smarty->assign('empty','true');
  }


  if ($_SESSION['cart']->count_contents() > 0) {
    $total_price =xtc_format_price($_SESSION['cart']->show_total(), $price_special = 0, $calculate_currencies = false);
    if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == '1' && $_SESSION['customers_status']['customers_status_ot_discount'] != '0.00' ) {
      $box_smarty->assign('TOTAL',xtc_format_price(($total_price), $price_special = 1, $calculate_currencies = false));
      $box_smarty->assign('DISCOUNT',xtc_format_price(xtc_recalculate_price(($total_price*(-1)), $_SESSION['customers_status']['customers_status_ot_discount']), $price_special = 1, $calculate_currencies = false));
    } else {
      $box_smarty->assign('TOTAL',xtc_format_price(($total_price), $price_special = 1, $calculate_currencies = false));
    }

  }


    $box_smarty->assign('LINK_CART',xarModURL('commerce','user','shopping_cart', '', 'SSL'));
    $box_smarty->assign('products',$products_in_cart);
/*
    $box_smarty->caching = 0;
    $box_smarty->assign('language', $_SESSION['language']);
    $box_shopping_cart= $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_cart.html');
*/
    $blockinfo['content'] = $data;
    return $blockinfo;
}
?>
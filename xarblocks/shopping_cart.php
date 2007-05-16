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
function carts_shopping_cartblock_init()
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
function carts_shopping_cartblock_info()
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
function carts_shopping_cartblock_display($blockinfo)
{
    // Security Check
    if (!xarSecurityCheck('ViewCartsBlocks', 0, 'Block', "content:$blockinfo[title]:All")) {return;}


    include_once 'modules/carts/xarclasses/shopping_cart.php';
    include_once 'modules/carts/xarclasses/shopping_cart_anonymous.php';


        $user = xarModAPIFunc('roles', 'user', 'get', array ('uid' => xarSession::getVar('uid')));
    if ($user['uname'] == 'anonymous'){
        $cart = new shoppingCartAnonymous();
        $link = "shopping_cart_anonymous";
    }
    else{
        //Test if we transfer anonymous basket to login
        if (xarSession::getVar('basket')){
            $cart = xarModAPIFunc('carts', 'user', 'savebasket_anonymous_to_login');
        }
        else{
            //$cart = new shoppingCart();
            $cart = new shoppingCart();
            $link = "shopping_cart_login";
        }
    }

     if ($cart->count_contents() > 0) {
    //We get all products in the basket
        $products_in_cart = $cart->get_products();

        //We get the total of the basket
        $total = $cart->calculate();


        $data['products'] = $products_in_cart;
        $data['total'] = $cart->show_total();
        $data['cart_empty'] = false;
    }
    else {
        // cart empty
        $data['cart_empty'] = 'true';
    }

/*
    if ($cart->count_contents() > 0) {
        $total_price = xarModAPIFunc('commerce','user','format_price',
                                            array('price_string' => $cart->show_total(),
                                            'price_special' => $price_special = 0, 'calculate_surrencies' => $calculate_currencies = false));
        if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == '1' && $_SESSION['customers_status']['customers_status_ot_discount'] != '0.00' ) {
            $data['total'] = xarModAPIFunc('commerce','user','format_price',
                                                array('price_string' => $total_price,
                                                'price_special' => $price_special = 1, 'calculate_surrencies' => $calculate_currencies = false));
            $data['discount'] = xarModAPIFunc('commerce','user','format_price',
                                                array('price_string' => xarModAPIFunc('commerce','user','recalculate_price', array('price' => $total_price*(-1))),
                                                'price_special' => $_SESSION['customers_status']['customers_status_ot_discount'],
                                                'calculate_currencies' => $price_special = 1, 'show_currencies' => $calculate_currencies = false));
        }
        else {
            $data['total'] = xarModAPIFunc('commerce','user','format_price',
                                                array('price_string' => $total_price,
                                                'price_special' => $price_special = 1, 'calculate_surrencies' => $calculate_currencies = false));
        }
    }*/


    $data['shopping_cart'] = xarModURL('carts','user',$link);

/*
    $box_shopping_cart= $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_cart.html');
*/
$data['empty'] = true;
    $blockinfo['content'] = $data;
    return $blockinfo;
}
?>
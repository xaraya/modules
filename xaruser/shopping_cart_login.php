<?php
// ----------------------------------------------------------------------
// Copyright (C) 2006: Fabien Bel (fab@webu.fr)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003 XT-Commerce
//   Third Party contributions:
//   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------

    require_once('modules/carts/xarclasses/shopping_cart.php');


  // include needed functions
//  require_once(DIR_FS_INC . 'xtc_array_to_string.inc.php');
//  require_once(DIR_FS_INC . 'xtc_recalculate_price.inc.php');

function carts_user_shopping_cart_login()
{


/*
    $languages = xarModAPIFunc('commerce','user','get_languages');
    $localeinfo = xarLocaleGetInfo(xarMLSGetSiteLocale());
    $data['language'] = $localeinfo['lang'] . "_" . $localeinfo['country'];
*/
    if (!xarVarFetch('action', 'isset', $action, null, XARVAR_NOT_REQUIRED)) return 0;
    
    //$mess may contains an error message 
    $mess = null;
    
    //Test if we transfer anonymous basket to login
    if (xarSessionGetVar('basket')){
            $cart = xarModAPIFunc('carts', 'user', 'savebasket_anonymous_to_login'); 
    }
    else{
            //$cart = new shoppingCart();
            $cart = new shoppingCart();
    }
    
    if ($cart->count_contents() > 0) {
        
     //We do action on the basket if the user asks  
       if ($action){
           
           $mess = xarModAPIFunc('carts', 'user', 'action_on_carts', array( 'action' => $action,
                                                                    'cart' => $cart));
       }
       
       //reload basket with modifications
        $cart->load_basket();   
        
        if ($cart->count_contents() > 0) {                
            //We get all products in the basket
            $products = $cart->get_products();
            
            //We get the total of the basket
            $cart->calculate();
            
            $data['info_message'] = $mess;
            $data['products'] = $products;
            
            $data['total'] = $cart->show_total();
            $data['cart_empty'] = false;
        }
        else{
            // empty cart
            $data['cart_empty'] = true;
        }

    } else {
        // empty cart
        $data['cart_empty'] = true;
    }
    
   //The type of the basket
   $data['shopping_cart'] = "shopping_cart_login";
   
   return xartplmodule('carts', 'user', 'shopping_cart', $data);
}
?>
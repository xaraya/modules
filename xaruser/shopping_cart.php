<?php
// ----------------------------------------------------------------------
// Copyright (C) 2005: Fabien Bel (fab@webu.fr)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003 XT-Commerce
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------
include_once 'modules/carts/xarclasses/shopping_cart.php';
include_once 'modules/carts/xarclasses/shopping_cart_anonymous.php';

/**
* Consult the good basket
* @param $args['iid'] ID of the art
* @param $args['extrainfo'] extra information
* @author Fabien Bel fab@webu.fr
**/
function carts_user_shopping_cart($args)
{
    extract($args);    
        
    //We get infos about user because we want to know if the person is an anonymous
    $user = xarModAPIFunc('roles', 'user', 'get', array ('uid' => xarSessionGetVar('uid')));

   
   if ($user['uname'] == 'anonymous'){
         $redirect = xarModUrl('carts','user', 'shopping_cart_anonymous');
   }
    else{
        $redirect = xarModUrl('carts','user', 'shopping_cart_login');
        
    }
     
   return (xarresponseredirect($redirect));
}


?>

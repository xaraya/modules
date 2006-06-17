<?php
// ----------------------------------------------------------------------
// Copyright (C) 2006: Marc Lutolf (mfl@netspan.ch)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003 XT-Commerce
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------

function carts_user_shopping_cart()
{
	if (xarUserIsLoggedIn()){
		include_once 'modules/carts/xarclasses/shopping_cart.php';
		xarResponseRedirect(xarModURL('carts','user', 'shopping_cart_login'));
	} else{
		include_once 'modules/carts/xarclasses/shopping_cart_anonymous.php';
		xarResponseRedirect(xarModURL('carts','user', 'shopping_cart_anonymous'));
	}
	return true;
}


?>

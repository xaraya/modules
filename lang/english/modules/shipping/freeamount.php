<?php
/* -----------------------------------------------------------------------------------------
   $Id: freeamount.php,v 1.1 2003/12/19 13:19:08 fanta2k Exp $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce( freeamount.php,v 1.01 2002/01/24 03:25:00); www.oscommerce.com 
   (c) 2003	 nextcommerce (freeamount.php,v 1.4 2003/08/13); www.nextcommerce.org

   Released under the GNU General Public License 
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   freeamountv2-p1         	Autor:	dwk

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

define('MODULE_SHIPPING_FREECOUNT_TEXT_TITLE', 'Free Shipping');
define('MODULE_SHIPPING_FREECOUNT_TEXT_DESCRIPTION', 'Free Shipping w/ Minimum Order Amount');
define('MODULE_SHIPPING_FREECOUNT_TEXT_WAY', 'w/ $' . MODULE_SHIPPING_FREECOUNT_AMOUNT . 'USD minimum order');
define('MODULE_SHIPPING_FREECOUNT_SORT_ORDER', 'Sort Order');

define('MODULE_SHIPPING_FREEAMOUNT_ALLOWED_TITLE' , 'Allowed Zones');
define('MODULE_SHIPPING_FREEAMOUNT_ALLOWED_DESC' , 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))');
define('MODULE_SHIPPING_FREECOUNT_STATUS_TITLE' , 'Enable Free Shipping with Minimum Purchase');
define('MODULE_SHIPPING_FREECOUNT_STATUS_DESC' , 'Do you want to offer free shipping?');
define('MODULE_SHIPPING_FREECOUNT_DISPLAY_TITLE' , 'Enable Display');
define('MODULE_SHIPPING_FREECOUNT_DISPLAY_DESC' , 'Do you want to display text way if the minimum amount is not reached?');
define('MODULE_SHIPPING_FREECOUNT_AMOUNT_TITLE' , 'Minimum Cost');
define('MODULE_SHIPPING_FREECOUNT_AMOUNT_DESC' , 'Minimum order amount purchased before shipping is free?');
define('MODULE_SHIPPING_FREECOUNT_SORT_ORDER_TITLE' , 'Display order');
define('MODULE_SHIPPING_FREECOUNT_SORT_ORDER_DESC' , 'Lowest will be displayed first.');
?>

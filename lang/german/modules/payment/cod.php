<?php
/* -----------------------------------------------------------------------------------------
   $Id: cod.php,v 1.1 2003/09/28 14:38:01 fanta2k Exp $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(cod.php,v 1.7 2002/04/17); www.oscommerce.com 
   (c) 2003	 nextcommerce (cod.php,v 1.5 2003/08/13); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
  define('MODULE_PAYMENT_TYPE_PERMISSION', 'cod');
  define('MODULE_PAYMENT_COD_TEXT_TITLE', 'Nachnahme');
  define('MODULE_PAYMENT_COD_TEXT_DESCRIPTION', 'Nachnahme');
  
  define('MODULE_PAYMENT_COD_ZONE_TITLE' , 'Payment Zone');
define('MODULE_PAYMENT_COD_ZONE_DESC' , 'If a zone is selected, only enable this payment method for that zone.');
define('MODULE_PAYMENT_COD_ALLOWED_TITLE' , 'Einzelne Zonen');
define('MODULE_PAYMENT_COD_ALLOWED_DESC' , 'Geben Sie <b>einzeln</b> die Zonen an, welche dieses Modul benützen dürfen. zb AT,DE (wenn leer, werden alle Zonen erlaubt)');
define('MODULE_PAYMENT_COD_STATUS_TITLE' , 'Enable Cash On Delivery Module');
define('MODULE_PAYMENT_COD_STATUS_DESC' , 'Do you want to accept Cash On Delevery payments?');
define('MODULE_PAYMENT_COD_SORT_ORDER_TITLE' , 'Sort order of display.');
define('MODULE_PAYMENT_COD_SORT_ORDER_DESC' , 'Sort order of display. Lowest is displayed first.');
define('MODULE_PAYMENT_COD_ORDER_STATUS_ID_TITLE' , 'Set Order Status');
define('MODULE_PAYMENT_COD_ORDER_STATUS_ID_DESC' , 'Set the status of orders made with this payment module to this value');
?>
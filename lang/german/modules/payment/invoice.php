<?
/* -----------------------------------------------------------------------------------------
   $Id: invoice.php,v 1.1 2003/09/28 14:38:01 fanta2k Exp $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(cod.php,v 1.28 2003/02/14); www.oscommerce.com
   (c) 2003	 nextcommerce (invoice.php,v 1.4 2003/08/13); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  define('MODULE_PAYMENT_INVOICE_TEXT_DESCRIPTION', 'Rechnung');
  define('MODULE_PAYMENT_INVOICE_TEXT_TITLE', 'Rechnung');
  
  define('MODULE_PAYMENT_INVOICE_STATUS_TITLE' , 'Enable Invoices');
define('MODULE_PAYMENT_INVOICE_STATUS_DESC' , 'Do you want to accept Invoices as payments?');
define('MODULE_PAYMENT_INVOICE_ORDER_STATUS_ID_TITLE' , 'Set Order Status');
define('MODULE_PAYMENT_INVOICE_ORDER_STATUS_ID_DESC' , 'Set the status of orders made with this payment module to this value');
define('MODULE_PAYMENT_INVOICE_SORT_ORDER_TITLE' , 'Sort order of display.');
define('MODULE_PAYMENT_INVOICE_SORT_ORDER_DESC' , 'Sort order of display. Lowest is displayed first.');
define('MODULE_PAYMENT_INVOICE_ZONE_TITLE' , 'Payment Zone');
define('MODULE_PAYMENT_INVOICE_ZONE_DESC' , 'If a zone is selected, only enable this payment method for that zone.');
define('MODULE_PAYMENT_INVOICE_ALLOWED_TITLE' , 'Einzelne Zonen');
define('MODULE_PAYMENT_INVOICE_ALLOWED_DESC' , 'Geben Sie <b>einzeln</b> die Zonen an, welche dieses Modul benützen dürfen. zb AT,DE (wenn leer, werden alle Zonen erlaubt)');
?>

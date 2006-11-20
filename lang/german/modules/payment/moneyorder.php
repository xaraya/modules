<?php
/* -----------------------------------------------------------------------------------------
   $Id: moneyorder.php,v 1.1 2003/09/28 14:38:01 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(moneyorder.php,v 1.8 2003/02/16); www.oscommerce.com
   (c) 2003  nextcommerce (moneyorder.php,v 1.4 2003/08/13); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  define('MODULE_PAYMENT_MONEYORDER_TEXT_TITLE', 'Scheck/Vorkasse');
  define('MODULE_PAYMENT_MONEYORDER_TEXT_DESCRIPTION', 'Zahlbar an:&#160;' . MODULE_PAYMENT_MONEYORDER_PAYTO . '<br>Adressat:<br><br>' . nl2br(STORE_NAME_ADDRESS) . '<br><br>' . 'Ihre Bestellung wird nicht versandt, bis wir das Geld erhalten haben!');
  define('MODULE_PAYMENT_MONEYORDER_TEXT_EMAIL_FOOTER', "Zahlbar an: ". MODULE_PAYMENT_MONEYORDER_PAYTO . "\n\nAdressat:\n" . STORE_NAME_ADDRESS . "\n\n" . 'Ihre Bestellung wir nicht versandt, bis wird das Geld erhalten haben!');

  define('MODULE_PAYMENT_MONEYORDER_STATUS_TITLE' , 'Enable Check/Money Order Module');
define('MODULE_PAYMENT_MONEYORDER_STATUS_DESC' , 'Do you want to accept Check/Money Order payments?');
define('MODULE_PAYMENT_MONEYORDER_ALLOWED_TITLE' , 'Einzelne Zonen');
define('MODULE_PAYMENT_MONEYORDER_ALLOWED_DESC' , 'Geben Sie <b>einzeln</b> die Zonen an, welche dieses Modul benützen dürfen. zb AT,DE (wenn leer, werden alle Zonen erlaubt)');
define('MODULE_PAYMENT_MONEYORDER_PAYTO_TITLE' , 'Make Payable to:');
define('MODULE_PAYMENT_MONEYORDER_PAYTO_DESC' , 'Who should payments be made payable to?');
define('MODULE_PAYMENT_MONEYORDER_SORT_ORDER_TITLE' , 'Sort order of display.');
define('MODULE_PAYMENT_MONEYORDER_SORT_ORDER_DESC' , 'Sort order of display. Lowest is displayed first.');
define('MODULE_PAYMENT_MONEYORDER_ZONE_TITLE' , 'Payment Zone');
define('MODULE_PAYMENT_MONEYORDER_ZONE_DESC' , 'If a zone is selected, only enable this payment method for that zone.');
define('MODULE_PAYMENT_MONEYORDER_ORDER_STATUS_ID_TITLE' , 'Set Order Status');
define('MODULE_PAYMENT_MONEYORDER_ORDER_STATUS_ID_DESC' , 'Set the status of orders made with this payment module to this value');
?>
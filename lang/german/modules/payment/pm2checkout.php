<?php
/* -----------------------------------------------------------------------------------------
   $Id: pm2checkout.php,v 1.1 2003/09/28 14:38:01 fanta2k Exp $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(pm2checkout.php,v 1.4 2002/11/01); www.oscommerce.com 
   (c) 2003	 nextcommerce (pm2checkout.php,v 1.4 2003/08/13); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

  define('MODULE_PAYMENT_2CHECKOUT_TEXT_TITLE', '2CheckOut');
  define('MODULE_PAYMENT_2CHECKOUT_TEXT_DESCRIPTION', 'Kreditkarten Test Info:<br><br>CC#: 4111111111111111<br>G&uuml;ltig bis: Any');
  define('MODULE_PAYMENT_2CHECKOUT_TEXT_TYPE', 'Typ:');
  define('MODULE_PAYMENT_2CHECKOUT_TEXT_CREDIT_CARD_OWNER', 'Kreditkarteninhaber:');
  define('MODULE_PAYMENT_2CHECKOUT_TEXT_CREDIT_CARD_OWNER_FIRST_NAME', 'Kreditkarteninhaber Vorname:');
  define('MODULE_PAYMENT_2CHECKOUT_TEXT_CREDIT_CARD_OWNER_LAST_NAME', 'Kreditkarteninhaber Nachname:');
  define('MODULE_PAYMENT_2CHECKOUT_TEXT_CREDIT_CARD_NUMBER', 'Kreditkarten-Nr.:');
  define('MODULE_PAYMENT_2CHECKOUT_TEXT_CREDIT_CARD_EXPIRES', 'G&uuml;ltig bis:');
  define('MODULE_PAYMENT_2CHECKOUT_TEXT_CREDIT_CARD_CHECKNUMBER', 'Karten-Pr&uuml;fnummer:');
  define('MODULE_PAYMENT_2CHECKOUT_TEXT_CREDIT_CARD_CHECKNUMBER_LOCATION', '(Auf der Kartenr&uuml;ckseite im Unterschriftsfeld)');
  define('MODULE_PAYMENT_2CHECKOUT_TEXT_JS_CC_NUMBER', '* Die \'Kreditkarten-Nr.\' muss mindestens aus ' . CC_NUMBER_MIN_LENGTH . ' Zahlen bestehen.\n');
  define('MODULE_PAYMENT_2CHECKOUT_TEXT_ERROR_MESSAGE', 'Bei der &Uuml;berp&uuml;fung Ihrer Kreditkarte ist ein Fehler aufgetreten! Bitte versuchen Sie es nochmal.');
  define('MODULE_PAYMENT_2CHECKOUT_TEXT_ERROR', 'Fehler bei der &Uuml;berp&uuml;fung der Kreditkarte!');
  
  define('MODULE_PAYMENT_2CHECKOUT_STATUS_TITLE' , 'Enable 2CheckOut Module');
define('MODULE_PAYMENT_2CHECKOUT_STATUS_DESC' , 'Do you want to accept 2CheckOut payments?');
define('MODULE_PAYMENT_2CHECKOUT_ALLOWED_TITLE' , 'Einzelne Zonen');
define('MODULE_PAYMENT_2CHECKOUT_ALLOWED_DESC' , 'Geben Sie <b>einzeln</b> die Zonen an, welche dieses Modul benützen dürfen. zb AT,DE (wenn leer, werden alle Zonen erlaubt)');
define('MODULE_PAYMENT_2CHECKOUT_LOGIN_TITLE' , 'Login/Store Number');
define('MODULE_PAYMENT_2CHECKOUT_LOGIN_DESC' , 'Login/Store Number used for the 2CheckOut service');
define('MODULE_PAYMENT_2CHECKOUT_TESTMODE_TITLE' , 'Transaction Mode');
define('MODULE_PAYMENT_2CHECKOUT_TESTMODE_DESC' , 'Transaction mode used for the 2Checkout service');
define('MODULE_PAYMENT_2CHECKOUT_EMAIL_MERCHANT_TITLE' , 'Merchant Notifications');
define('MODULE_PAYMENT_2CHECKOUT_EMAIL_MERCHANT_DESC' , 'Should 2CheckOut e-mail a receipt to the store owner?');
define('MODULE_PAYMENT_2CHECKOUT_SORT_ORDER_TITLE' , 'Sort order of display.');
define('MODULE_PAYMENT_2CHECKOUT_SORT_ORDER_DESC' , 'Sort order of display. Lowest is displayed first.');
define('MODULE_PAYMENT_2CHECKOUT_ZONE_TITLE' , 'Payment Zone');
define('MODULE_PAYMENT_2CHECKOUT_ZONE_DESC' , 'If a zone is selected, only enable this payment method for that zone.');
define('MODULE_PAYMENT_2CHECKOUT_ORDER_STATUS_ID_TITLE' , 'Set Order Status');
define('MODULE_PAYMENT_2CHECKOUT_ORDER_STATUS_ID_DESC' , 'Set the status of orders made with this payment module to this value');
?>
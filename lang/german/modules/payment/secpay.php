<?php
/* -----------------------------------------------------------------------------------------
   $Id: secpay.php,v 1.1 2003/09/28 14:38:01 fanta2k Exp $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce 
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(secpay.php,v 1.8 2002/11/01); www.oscommerce.com 
   (c) 2003	 nextcommerce (secpay.php,v 1.4 2003/08/13); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

  define('MODULE_PAYMENT_SECPAY_TEXT_TITLE', 'SECPay');
  define('MODULE_PAYMENT_SECPAY_TEXT_DESCRIPTION', 'Kreditkarten Test Info:<br><br>CC#: 4444333322221111<br>G&uuml;ltig bis: Any');
  define('MODULE_PAYMENT_SECPAY_TEXT_ERROR', 'Fehler bei der &Uuml;berp&uuml;fung der Kreditkarte!');
  define('MODULE_PAYMENT_SECPAY_TEXT_ERROR_MESSAGE', 'Bei der &Uuml;berp&uuml;fung Ihrer Kreditkarte ist ein Fehler aufgetreten! Bitte versuchen Sie es nochmal.');
  
  define('MODULE_PAYMENT_SECPAY_MERCHANT_ID_TITLE' , 'Merchant ID');
define('MODULE_PAYMENT_SECPAY_MERCHANT_ID_DESC' , 'Merchant ID to use for the SECPay service');
define('MODULE_PAYMENT_SECPAY_ALLOWED_TITLE' , 'Einzelne Zonen');
define('MODULE_PAYMENT_SECPAY_ALLOWED_DESC' , 'Geben Sie <b>einzeln</b> die Zonen an, welche dieses Modul benützen dürfen. zb AT,DE (wenn leer, werden alle Zonen erlaubt)');
define('MODULE_PAYMENT_SECPAY_STATUS_TITLE' , 'Enable SECpay Module');
define('MODULE_PAYMENT_SECPAY_STATUS_DESC' , 'Do you want to accept SECPay payments?');
define('MODULE_PAYMENT_SECPAY_CURRENCY_TITLE' , 'Transaction Currency');
define('MODULE_PAYMENT_SECPAY_CURRENCY_DESC' , 'The currency to use for credit card transactions');
define('MODULE_PAYMENT_SECPAY_TEST_STATUS_TITLE' , 'Transaction Mode');
define('MODULE_PAYMENT_SECPAY_TEST_STATUS_DESC' , 'Transaction mode to use for the SECPay service');
define('MODULE_PAYMENT_SECPAY_SORT_ORDER_TITLE' , 'Sort order of display.');
define('MODULE_PAYMENT_SECPAY_SORT_ORDER_DESC' , 'Sort order of display. Lowest is displayed first.');
define('MODULE_PAYMENT_SECPAY_ZONE_TITLE' , 'Payment Zone');
define('MODULE_PAYMENT_SECPAY_ZONE_DESC' , 'If a zone is selected, only enable this payment method for that zone.');
define('MODULE_PAYMENT_SECPAY_ORDER_STATUS_ID_TITLE' , 'Set Order Status');
define('MODULE_PAYMENT_SECPAY_ORDER_STATUS_ID_DESC' , 'Set the status of orders made with this payment module to this value');
?>
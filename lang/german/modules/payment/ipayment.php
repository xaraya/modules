<?php
/* -----------------------------------------------------------------------------------------
   $Id: ipayment.php,v 1.1 2003/09/28 14:38:01 fanta2k Exp $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ipayment.php,v 1.6 2002/11/01); www.oscommerce.com 
   (c) 2003	 nextcommerce (ipayment.php,v 1.4 2003/08/13); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

  define('MODULE_PAYMENT_IPAYMENT_TEXT_TITLE', 'iPayment');
  define('MODULE_PAYMENT_IPAYMENT_TEXT_DESCRIPTION', 'Kreditkarten Test Info:<br><br>CC#: 4111111111111111<br>G&uuml;ltig bis: Any');
  define('IPAYMENT_ERROR_HEADING', 'Folgender Fehler wurde von iPayment w&auml;hrend des Prozesses gemeldet:');
  define('IPAYMENT_ERROR_MESSAGE', 'Bitte kontrollieren Sie die Daten Ihrer Kreditkarte!');
  define('MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_OWNER', 'Kreditkarteninhaber');
  define('MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_NUMBER', 'Kreditkarten-Nr.:');
  define('MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_EXPIRES', 'G&uuml;ltig bis:');
  define('MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_CHECKNUMBER', 'Karten-Pr&uuml;fnummer');
  define('MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_CHECKNUMBER_LOCATION', '(Auf der Kartenr&uuml;ckseite im Unterschriftsfeld)');

  define('MODULE_PAYMENT_IPAYMENT_TEXT_JS_CC_OWNER', '* Der Name des Kreditkarteninhabers mss mindestens aus  ' . CC_OWNER_MIN_LENGTH . ' Zeichen bestehen.\n');
  define('MODULE_PAYMENT_IPAYMENT_TEXT_JS_CC_NUMBER', '* Die \'Kreditkarten-Nr.\' muss mindestens aus ' . CC_NUMBER_MIN_LENGTH . ' Zahlen bestehen.\n');
  
  define('MODULE_PAYMENT_IPAYMENT_ALLOWED_TITLE' , 'Einzelne Zonen');
define('MODULE_PAYMENT_IPAYMENT_ALLOWED_DESC' , 'Geben Sie <b>einzeln</b> die Zonen an, welche dieses Modul benützen dürfen. zb AT,DE (wenn leer, werden alle Zonen erlaubt)');
define('MODULE_PAYMENT_IPAYMENT_ID_TITLE' , 'Account Number');
define('MODULE_PAYMENT_IPAYMENT_ID_DESC' , 'The account number used for the iPayment service');
define('MODULE_PAYMENT_IPAYMENT_STATUS_TITLE' , 'Enable iPayment Module');
define('MODULE_PAYMENT_IPAYMENT_STATUS_DESC' , 'Do you want to accept iPayment payments?');
define('MODULE_PAYMENT_IPAYMENT_PASSWORD_TITLE' , 'User Password');
define('MODULE_PAYMENT_IPAYMENT_PASSWORD_DESC' , 'The user password for the iPayment service');
define('MODULE_PAYMENT_IPAYMENT_USER_ID_TITLE' , 'User ID');
define('MODULE_PAYMENT_IPAYMENT_USER_ID_DESC' , 'The user ID for the iPayment service');
define('MODULE_PAYMENT_IPAYMENT_CURRENCY_TITLE' , 'Transaction Currency');
define('MODULE_PAYMENT_IPAYMENT_CURRENCY_DESC' , 'The currency to use for credit card transactions');
define('MODULE_PAYMENT_IPAYMENT_SORT_ORDER_TITLE' , 'Sort order of display.');
define('MODULE_PAYMENT_IPAYMENT_SORT_ORDER_DESC' , 'Sort order of display. Lowest is displayed first.');
define('MODULE_PAYMENT_IPAYMENT_ZONE_TITLE' , 'Payment Zone');
define('MODULE_PAYMENT_IPAYMENT_ZONE_DESC' , 'If a zone is selected, only enable this payment method for that zone.');
define('MODULE_PAYMENT_IPAYMENT_ORDER_STATUS_ID_TITLE' , 'Set Order Status');
define('MODULE_PAYMENT_IPAYMENT_ORDER_STATUS_ID_DESC' , 'Set the status of orders made with this payment module to this value');
?>
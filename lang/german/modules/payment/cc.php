<?php
/* -----------------------------------------------------------------------------------------
   $Id: cc.php,v 1.1 2003/09/28 14:38:01 fanta2k Exp $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce 
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(cc.php,v 1.11 2003/02/16); www.oscommerce.com 
   (c) 2003	 nextcommerce (cc.php,v 1.5 2003/08/13); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
  define('MODULE_PAYMENT_TYPE_PERMISSION', 'cc');
  define('MODULE_PAYMENT_CC_TEXT_TITLE', 'Kreditkarte');
  define('MODULE_PAYMENT_CC_TEXT_DESCRIPTION', 'Kreditkarten Test Info:<br><br>CC#: 4111111111111111<br>G&uuml;ltig bis: Any');
  define('MODULE_PAYMENT_CC_TEXT_CREDIT_CARD_TYPE', 'Typ:');
  define('MODULE_PAYMENT_CC_TEXT_CREDIT_CARD_OWNER', 'Kreditkarteninhaber:');
  define('MODULE_PAYMENT_CC_TEXT_CREDIT_CARD_NUMBER', 'Kreditkarten-Nr.:');
  define('MODULE_PAYMENT_CC_TEXT_CREDIT_CARD_EXPIRES', 'G&uuml;ltig bis:');
  define('MODULE_PAYMENT_CC_TEXT_JS_CC_OWNER', '* Der \'Name des Inhabers\' muss mindestens aus ' . CC_OWNER_MIN_LENGTH . ' Buchstaben bestehen.\n');
  define('MODULE_PAYMENT_CC_TEXT_JS_CC_NUMBER', '* Die \'Kreditkarten-Nr.\' muss mindestens aus ' . CC_NUMBER_MIN_LENGTH . ' Zahlen bestehen.\n');
  define('MODULE_PAYMENT_CC_TEXT_ERROR', 'Fehler bei der &Uuml;berp&uuml;fung der Kreditkarte!');
  
  define('MODULE_PAYMENT_CC_STATUS_TITLE' , 'Enable Credit Card Module');
define('MODULE_PAYMENT_CC_STATUS_DESC' , 'Do you want to accept credit card payments?');
define('MODULE_PAYMENT_CC_ALLOWED_TITLE' , 'Einzelne Zonen');
define('MODULE_PAYMENT_CC_ALLOWED_DESC' , 'Geben Sie <b>einzeln</b> die Zonen an, welche dieses Modul benützen dürfen. zb AT,DE (wenn leer, werden alle Zonen erlaubt)');
define('MODULE_PAYMENT_CC_EMAIL_TITLE' , 'Split Credit Card E-Mail Address');
define('MODULE_PAYMENT_CC_EMAIL_DESC' , 'If an e-mail address is entered, the middle digits of the credit card number will be sent to the e-mail address (the outside digits are stored in the database with the middle digits censored)');
define('MODULE_PAYMENT_CC_SORT_ORDER_TITLE' , 'Sort order of display.');
define('MODULE_PAYMENT_CC_SORT_ORDER_DESC' , 'Sort order of display. Lowest is displayed first.');
define('MODULE_PAYMENT_CC_ZONE_TITLE' , 'Payment Zone');
define('MODULE_PAYMENT_CC_ZONE_DESC' , 'If a zone is selected, only enable this payment method for that zone.');
define('MODULE_PAYMENT_CC_ORDER_STATUS_ID_TITLE' , 'Set Order Status');
define('MODULE_PAYMENT_CC_ORDER_STATUS_ID_DESC' , 'Set the status of orders made with this payment module to this value');
?>
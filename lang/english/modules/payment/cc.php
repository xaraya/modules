<?php
/* -----------------------------------------------------------------------------------------
   $Id: cc.php,v 1.1 2003/12/19 13:19:08 fanta2k Exp $   

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
  define('MODULE_PAYMENT_CC_TEXT_TITLE', 'Credit Card');
  define('MODULE_PAYMENT_CC_TEXT_DESCRIPTION', 'Credit Card Test Info:<br><br>CC#: 4111111111111111<br>Expiry: Any');
  define('MODULE_PAYMENT_CC_TEXT_CREDIT_CARD_TYPE', 'Credit Card Type:');
  define('MODULE_PAYMENT_CC_TEXT_CREDIT_CARD_OWNER', 'Credit Card Owner:');
  define('MODULE_PAYMENT_CC_TEXT_CREDIT_CARD_NUMBER', 'Credit Card Number:');
  define('MODULE_PAYMENT_CC_TEXT_CREDIT_CARD_EXPIRES', 'Credit Card Expiry Date:');
  define('MODULE_PAYMENT_CC_TEXT_JS_CC_OWNER', '* The owner\'s name of the credit card must be at least ' . CC_OWNER_MIN_LENGTH . ' characters.\n');
  define('MODULE_PAYMENT_CC_TEXT_JS_CC_NUMBER', '* The credit card number must be at least ' . CC_NUMBER_MIN_LENGTH . ' characters.\n');
  define('MODULE_PAYMENT_CC_TEXT_ERROR', 'Credit Card Error!');

  define('MODULE_PAYMENT_CC_STATUS_TITLE' , 'Enable Credit Card Module');
define('MODULE_PAYMENT_CC_STATUS_DESC' , 'Do you want to accept credit card payments?');
define('MODULE_PAYMENT_CC_ALLOWED_TITLE' , 'Allowed zones');
define('MODULE_PAYMENT_CC_ALLOWED_DESC' , 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))');
define('MODULE_PAYMENT_CC_EMAIL_TITLE' , 'Split Credit Card E-Mail Address');
define('MODULE_PAYMENT_CC_EMAIL_DESC' , 'If an e-mail address is entered, the middle digits of the credit card number will be sent to the e-mail address (the outside digits are stored in the database with the middle digits censored)');
define('MODULE_PAYMENT_CC_SORT_ORDER_TITLE' , 'Sort order of display.');
define('MODULE_PAYMENT_CC_SORT_ORDER_DESC' , 'Sort order of display. Lowest is displayed first.');
define('MODULE_PAYMENT_CC_ZONE_TITLE' , 'Payment Zone');
define('MODULE_PAYMENT_CC_ZONE_DESC' , 'If a zone is selected, only enable this payment method for that zone.');
define('MODULE_PAYMENT_CC_ORDER_STATUS_ID_TITLE' , 'Set Order Status');
define('MODULE_PAYMENT_CC_ORDER_STATUS_ID_DESC' , 'Set the status of orders made with this payment module to this value');
?>

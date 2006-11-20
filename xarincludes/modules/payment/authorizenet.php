<?php
/* -----------------------------------------------------------------------------------------
   $Id: authorizenet.php,v 1.1 2003/09/06 22:13:54 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(authorizenet.php,v 1.48 2003/04/10); www.oscommerce.com
   (c) 2003  nextcommerce (authorizenet.php,v 1.9 2003/08/23); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


  class authorizenet
  {

    var $code, $title, $description, $enabled;


    function authorizenet()
    {
      global $order;

      $this->code = 'authorizenet';
      $this->title = MODULE_PAYMENT_AUTHORIZENET_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_AUTHORIZENET_TEXT_DESCRIPTION;
      $this->enabled = ((MODULE_PAYMENT_AUTHORIZENET_STATUS == 'True') ? true : false);
      $this->sort_order = MODULE_PAYMENT_AUTHORIZENET_SORT_ORDER;

      if ((int)MODULE_PAYMENT_AUTHORIZENET_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_AUTHORIZENET_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();

      $this->form_action_url = 'https://secure.authorize.net/gateway/transact.dll';
    }

/**
 * Authorize.net utility functions
 * DISCLAIMER:
 *     This code is distributed in the hope that it will be useful, but without any warranty;
 *     without even the implied warranty of merchantability or fitness for a particular purpose.
 *
 * Main Interfaces:
 *
 * function InsertFP ($loginid, $txnkey, $amount, $sequence) - Insert HTML form elements required for SIM
 * function CalculateFP ($loginid, $txnkey, $amount, $sequence, $tstamp) - Returns Fingerprint.
 *
 * compute HMAC-MD5
 * Uses PHP mhash extension. Pl sure to enable the extension
 * function hmac ($key, $data) {
 * return (bin2hex (mhash(MHASH_MD5, $data, $key)));
 *
 * RFC 2104 HMAC implementation for php.
 * Creates an md5 HMAC
 * Eliminates the need to install mhash to compute a HMAC
 * Hacked by Lance Rushing
 * Thanks is lance from http://www.php.net/manual/en/function.mhash.php
 * lance_rushing at hot* spamfree *mail dot com
 *
 * @param string $key
 * @param string $data
 */
function hmac ($key, $data)
{
   $b = 64; // byte length for md5
   if (strlen($key) > $b) {
       $key = pack("H*",md5($key));
   }
   $key  = str_pad($key, $b, chr(0x00));
   $ipad = str_pad('', $b, chr(0x36));
   $opad = str_pad('', $b, chr(0x5c));
   $k_ipad = $key ^ $ipad ;
   $k_opad = $key ^ $opad;

   return md5($k_opad  . pack("H*",md5($k_ipad . $data)));
}

 /**
 * Calculate and return fingerprint
 * Use when you need control on the HTML output
 *
 * @param string $loginid
 * @param string $txnkey
 * @param string $amount
 * @param string $sequence
 * @param string $tstamp
 * @param string $currency
 */
function CalculateFP ($loginid, $txnkey, $amount, $sequence, $tstamp, $currency = "")
{
  return ($this->hmac ($txnkey, $loginid . "^" . $sequence . "^" . $tstamp . "^" . $amount . "^" . $currency));
}

 /**
 * Inserts the hidden variables in the HTML FORM required for SIM
 * Invokes hmac function to calculate fingerprint.
 */
function InsertFP ($loginid, $txnkey, $amount, $sequence, $currency = "")
{
  $tstamp = time ();
  $fingerprint = $this->hmac ($txnkey, $loginid . "^" . $sequence . "^" . $tstamp . "^" . $amount . "^" . $currency);

  $str = xtc_draw_hidden_field('x_fp_sequence', $sequence) .
         xtc_draw_hidden_field('x_fp_timestamp', $tstamp) .
         xtc_draw_hidden_field('x_fp_hash', $fingerprint);

  return $str;
}

/**
 * class methods
 */
    function update_status()
    {
      global $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_AUTHORIZENET_ZONE > 0) ) {
        $check_flag = false;
        $check_query = new xenQuery("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_AUTHORIZENET_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
      $q = new xenQuery();
      if(!$q->run()) return;
        while ($check = $q->output()) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check['zone_id'] == $order->billing['zone_id']) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag == false) {
          $this->enabled = false;
        }
      }
    }

    function javascript_validation()
    {
      $js = '  if (payment_value == "' . $this->code . '") {' . "\n" .
            '    var cc_owner = document.checkout_payment.authorizenet_cc_owner.value;' . "\n" .
            '    var cc_number = document.checkout_payment.authorizenet_cc_number.value;' . "\n" .
            '    if (cc_owner == "" || cc_owner.length < ' . CC_OWNER_MIN_LENGTH . ') {' . "\n" .
            '      error_message = error_message + "' . MODULE_PAYMENT_AUTHORIZENET_TEXT_JS_CC_OWNER . '";' . "\n" .
            '      error = 1;' . "\n" .
            '    }' . "\n" .
            '    if (cc_number == "" || cc_number.length < ' . CC_NUMBER_MIN_LENGTH . ') {' . "\n" .
            '      error_message = error_message + "' . MODULE_PAYMENT_AUTHORIZENET_TEXT_JS_CC_NUMBER . '";' . "\n" .
            '      error = 1;' . "\n" .
            '    }' . "\n" .
            '  }' . "\n";

      return $js;
    }

    function selection()
    {
      global $order;

      for ($i=1; $i<13; $i++) {
        $expires_month[] = array('id' => sprintf('%02d', $i), 'text' => strftime('%B',mktime(0,0,0,$i,1,2000)));
      }

      $today = getdate();
      for ($i=$today['year']; $i < $today['year']+10; $i++) {
        $expires_year[] = array('id' => strftime('%y',mktime(0,0,0,1,1,$i)), 'text' => strftime('%Y',mktime(0,0,0,1,1,$i)));
      }
      $selection = array('id' => $this->code,
                         'module' => $this->title,
                         'fields' => array(array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_CREDIT_CARD_OWNER,
                                                 'field' => xtc_draw_input_field('authorizenet_cc_owner', $order->billing['firstname'] . ' ' . $order->billing['lastname'])),
                                           array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_CREDIT_CARD_NUMBER,
                                                 'field' => xtc_draw_input_field('authorizenet_cc_number')),
                                           array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_CREDIT_CARD_EXPIRES,
                                                 'field' => commerce_userapi_draw_pull_down_menu('authorizenet_cc_expires_month', $expires_month) . '&#160;' . commerce_userapi_draw_pull_down_menu('authorizenet_cc_expires_year', $expires_year))));

      return $selection;
    }

    function pre_confirmation_check()
    {

      include(DIR_WS_CLASSES . 'cc_validation.php');

      $cc_validation = new cc_validation();
      $result = $cc_validation->validate($_POST['authorizenet_cc_number'], $_POST['authorizenet_cc_expires_month'], $_POST['authorizenet_cc_expires_year']);
      $error = '';
      switch ($result) {
        case -1:
          $error = sprintf(TEXT_CCVAL_ERROR_UNKNOWN_CARD, substr($cc_validation->cc_number, 0, 4));
          break;
        case -2:
        case -3:
        case -4:
          $error = TEXT_CCVAL_ERROR_INVALID_DATE;
          break;
        case false:
          $error = TEXT_CCVAL_ERROR_INVALID_NUMBER;
          break;
      }

      if ( ($result == false) || ($result < 1) ) {
        $payment_error_return = 'payment_error=' . $this->code . '&error=' . urlencode($error) . '&authorizenet_cc_owner=' . urlencode($_POST['authorizenet_cc_owner']) . '&authorizenet_cc_expires_month=' . $_POST['authorizenet_cc_expires_month'] . '&authorizenet_cc_expires_year=' . $_POST['authorizenet_cc_expires_year'];

        xarRedirectResponse(xarModURL('commerce','user',(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
      }

      $this->cc_card_type = $cc_validation->cc_type;
      $this->cc_card_number = $cc_validation->cc_number;
      $this->cc_expiry_month = $cc_validation->cc_expiry_month;
      $this->cc_expiry_year = $cc_validation->cc_expiry_year;
    }

    function confirmation()
    {

      $confirmation = array('title' => $this->title . ': ' . $this->cc_card_type,
                            'fields' => array(array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_CREDIT_CARD_OWNER,
                                                    'field' => $_POST['authorizenet_cc_owner']),
                                              array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_CREDIT_CARD_NUMBER,
                                                    'field' => substr($this->cc_card_number, 0, 4) . str_repeat('X', (strlen($this->cc_card_number) - 8)) . substr($this->cc_card_number, -4)),
                                              array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_CREDIT_CARD_EXPIRES,
                                                    'field' => strftime('%B, %Y', mktime(0,0,0,$_POST['authorizenet_cc_expires_month'], 1, '20' . $_POST['authorizenet_cc_expires_year'])))));

      return $confirmation;
    }

    function process_button()
    {
      global $order;

      $sequence = rand(1, 1000);
      $data['hidden'][0] = array('name' =>x_Login, 'value' =>MODULE_PAYMENT_AUTHORIZENET_LOGIN);
      $data['hidden'][1] = array('name' =>x_Card_Num, 'value' =>$this->cc_card_number);
      $data['hidden'][2] = array('name' =>x_Exp_Date, 'value' =>$this->cc_expiry_month . substr($this->cc_expiry_year, -2));
      $data['hidden'][3] = array('name' =>x_Amount, 'value' =>number_format($order->info['total'], 2));
      $data['hidden'][4] = array('name' =>x_Relay_URL, 'value' =>xarModURL('commerce','user',(FILENAME_CHECKOUT_PROCESS, '', 'SSL', false));
      $data['hidden'][5] = array('name' =>x_Method, 'value' =>((MODULE_PAYMENT_AUTHORIZENET_METHOD == 'Credit Card') ? 'CC' : 'ECHECK'));
      $data['hidden'][6] = array('name' =>x_Version, 'value' =>'3.0');
      $data['hidden'][7] = array('name' =>x_Cust_ID, 'value' =>$_SESSION['customer_id']);
      $data['hidden'][8] = array('name' =>x_Email_Customer, 'value' =>((MODULE_PAYMENT_AUTHORIZENET_EMAIL_CUSTOMER == 'True') ? 'TRUE': 'FALSE'));
      $data['hidden'][9] = array('name' =>x_first_name, 'value' =>$order->billing['firstname']);
      $data['hidden'][10] = array('name' =>x_last_name, 'value' =>$order->billing['lastname']);
      $data['hidden'][11] = array('name' =>x_address, 'value' =>$order->billing['street_address']);
      $data['hidden'][12] = array('name' =>x_city, 'value' =>$order->billing['city']);
      $data['hidden'][13] = array('name' =>x_state, 'value' =>$order->billing['state']);
      $data['hidden'][14] = array('name' =>x_zip, 'value' =>$order->billing['postcode']);
      $data['hidden'][15] = array('name' =>x_country, 'value' =>$order->billing['country']['title']);
      $data['hidden'][16] = array('name' =>x_phone, 'value' =>$order->customer['telephone']);
      $data['hidden'][17] = array('name' =>x_email, 'value' =>$order->customer['email_address']);
      $data['hidden'][18] = array('name' =>x_ship_to_first_name, 'value' =>$order->delivery['firstname']);
      $data['hidden'][19] = array('name' =>x_ship_to_last_name, 'value' =>$order->delivery['lastname']);
      $data['hidden'][20] = array('name' =>x_ship_to_address, 'value' =>$order->delivery['street_address']);
      $data['hidden'][21] = array('name' =>x_ship_to_city, 'value' =>$order->delivery['city']);
      $data['hidden'][22] = array('name' =>x_ship_to_state, 'value' =>$order->delivery['state']);
      $data['hidden'][23] = array('name' =>x_ship_to_zip, 'value' =>$order->delivery['postcode']);
      $data['hidden'][24] = array('name' =>x_ship_to_country, 'value' =>$order->delivery['country']['title']);
      $data['hidden'][25] = array('name' =>x_Customer_IP, 'value' =>$_SERVER['REMOTE_ADDR']);

        return $data;
        $process_button_string = $this->InsertFP(MODULE_PAYMENT_AUTHORIZENET_LOGIN, MODULE_PAYMENT_AUTHORIZENET_TXNKEY, number_format($order->info['total'], 2), $sequence);
      if (MODULE_PAYMENT_AUTHORIZENET_TESTMODE == 'Test') $process_button_string .= xtc_draw_hidden_field('x_Test_Request', 'TRUE');

      $process_button_string .= xtc_draw_hidden_field(xtc_session_name(), xtc_session_id());

      return $process_button_string;
    }

    function before_process()
    {

      if ($_POST['x_response_code'] == '1') return;
      if ($_POST['x_response_code'] == '2') {
        xarRedirectResponse(xarModURL('commerce','user',(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(MODULE_PAYMENT_AUTHORIZENET_TEXT_DECLINED_MESSAGE), 'SSL', true, false));
      }
      // Code 3 is an error - but anything else is an error too (IMHO)
      xarRedirectResponse(xarModURL('commerce','user',(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(MODULE_PAYMENT_AUTHORIZENET_TEXT_ERROR_MESSAGE), 'SSL', true, false));
    }

    function after_process()
    {
      return false;
    }

    function get_error()
    {

      $error = array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_ERROR,
                     'error' => stripslashes(urldecode($_GET['error'])));

      return $error;
    }

    function check()
    {
      if (!isset($this->_check)) {
        $check_query = new xenQuery("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_AUTHORIZENET_STATUS'");
        $this->_check = $check_query->getrows();
      }
      return $this->_check;
    }

    function install()
    {
      new xenQuery("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_AUTHORIZENET_STATUS', 'True', '6', '0', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_AUTHORIZENET_ALLOWED', '', '6', '0', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_AUTHORIZENET_LOGIN', 'testing',  '6', '0', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_AUTHORIZENET_TXNKEY', 'Test',  '6', '0', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_AUTHORIZENET_TESTMODE', 'Test',  '6', '0', 'xtc_cfg_select_option(array(\'Test\', \'Production\'), ', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_AUTHORIZENET_METHOD', 'Credit Card',  '6', '0', 'xtc_cfg_select_option(array(\'Credit Card\', \'eCheck\'), ', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_AUTHORIZENET_EMAIL_CUSTOMER', 'False',  '6', '0', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_AUTHORIZENET_SORT_ORDER', '0',  '6', '0', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_AUTHORIZENET_ZONE', '0',  '6', '2', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_AUTHORIZENET_ORDER_STATUS_ID', '0', '6', '0', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
    }

    function remove()
    {
      new xenQuery("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys()
    {
      return array('MODULE_PAYMENT_AUTHORIZENET_STATUS','MODULE_PAYMENT_AUTHORIZENET_ALLOWED', 'MODULE_PAYMENT_AUTHORIZENET_LOGIN', 'MODULE_PAYMENT_AUTHORIZENET_TXNKEY', 'MODULE_PAYMENT_AUTHORIZENET_TESTMODE', 'MODULE_PAYMENT_AUTHORIZENET_METHOD', 'MODULE_PAYMENT_AUTHORIZENET_EMAIL_CUSTOMER', 'MODULE_PAYMENT_AUTHORIZENET_ZONE', 'MODULE_PAYMENT_AUTHORIZENET_ORDER_STATUS_ID', 'MODULE_PAYMENT_AUTHORIZENET_SORT_ORDER');
    }
  }
?>

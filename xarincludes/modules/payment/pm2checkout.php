<?php
/* -----------------------------------------------------------------------------------------
   $Id: pm2checkout.php,v 1.1 2003/09/06 22:13:54 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(pm2checkout.php,v 1.19 2003/01/29); www.oscommerce.com
   (c) 2003  nextcommerce (pm2checkout.php,v 1.8 2003/08/24); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


  class pm2checkout
  {
    var $code, $title, $description, $enabled;


    function pm2checkout()
    {
      global $order;

      $this->code = 'pm2checkout';
      $this->title = MODULE_PAYMENT_2CHECKOUT_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_2CHECKOUT_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_PAYMENT_2CHECKOUT_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_2CHECKOUT_STATUS == 'True') ? true : false);

      if ((int)MODULE_PAYMENT_2CHECKOUT_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_2CHECKOUT_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();

      $this->form_action_url = 'https://www.2checkout.com/cgi-bin/Abuyers/purchase.2c';
    }


    function update_status()
    {
      global $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_2CHECKOUT_ZONE > 0) ) {
        $check_flag = false;
        $check_query = new xenQuery("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_2CHECKOUT_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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
            '    var cc_number = document.checkout_payment.pm_2checkout_cc_number.value;' . "\n" .
            '    if (cc_number == "" || cc_number.length < ' . CC_NUMBER_MIN_LENGTH . ') {' . "\n" .
            '      error_message = error_message + "' . MODULE_PAYMENT_2CHECKOUT_TEXT_JS_CC_NUMBER . '";' . "\n" .
            '      error = 1;' . "\n" .
            '    }' . "\n" .
            '  }' . "\n";

      return $js;
    }

    function selection() {
      global $order;

      for ($i=1; $i < 13; $i++) {
        $expires_month[] = array('id' => sprintf('%02d', $i), 'text' => strftime('%B',mktime(0,0,0,$i,1,2000)));
      }

      $today = getdate();
      for ($i=$today['year']; $i < $today['year']+10; $i++) {
        $expires_year[] = array('id' => strftime('%y',mktime(0,0,0,1,1,$i)), 'text' => strftime('%Y',mktime(0,0,0,1,1,$i)));
      }

      $selection = array('id' => $this->code,
                         'module' => $this->title,
                         'fields' => array(array('title' => MODULE_PAYMENT_2CHECKOUT_TEXT_CREDIT_CARD_OWNER_FIRST_NAME,
                                                 'field' => xtc_draw_input_field('pm_2checkout_cc_owner_firstname', $order->billing['firstname'])),
                                           array('title' => MODULE_PAYMENT_2CHECKOUT_TEXT_CREDIT_CARD_OWNER_LAST_NAME,
                                                 'field' => xtc_draw_input_field('pm_2checkout_cc_owner_lastname', $order->billing['lastname'])),
                                           array('title' => MODULE_PAYMENT_2CHECKOUT_TEXT_CREDIT_CARD_NUMBER,
                                                 'field' => xtc_draw_input_field('pm_2checkout_cc_number')),
                                           array('title' => MODULE_PAYMENT_2CHECKOUT_TEXT_CREDIT_CARD_EXPIRES,
                                                 'field' => commerce_userapi_draw_pull_down_menu('pm_2checkout_cc_expires_month', $expires_month) . '&#160;' . commerce_userapi_draw_pull_down_menu('pm_2checkout_cc_expires_year', $expires_year)),
                                           array('title' => MODULE_PAYMENT_2CHECKOUT_TEXT_CREDIT_CARD_CHECKNUMBER,
                                                 'field' => xtc_draw_input_field('pm_2checkout_cc_cvv', '', 'size="4" maxlength="3"') . '&#160;<small>' . MODULE_PAYMENT_2CHECKOUT_TEXT_CREDIT_CARD_CHECKNUMBER_LOCATION . '</small>')));

      return $selection;
    }

    function pre_confirmation_check()
    {

      include(DIR_WS_CLASSES . 'cc_validation.php');

      $cc_validation = new cc_validation();
      $result = $cc_validation->validate($_POST['pm_2checkout_cc_number'], $_POST['pm_2checkout_cc_expires_month'], $_POST['pm_2checkout_cc_expires_year']);

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
        $payment_error_return = 'payment_error=' . $this->code . '&error=' . urlencode($error) . '&pm_2checkout_cc_owner_firstname=' . urlencode($_POST['pm_2checkout_cc_owner_firstname']) . '&pm_2checkout_cc_owner_lastname=' . urlencode($_POST['pm_2checkout_cc_owner_lastname']) . '&pm_2checkout_cc_expires_month=' . $_POST['pm_2checkout_cc_expires_month'] . '&pm_2checkout_cc_expires_year=' . $_POST['pm_2checkout_cc_expires_year'];

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
                            'fields' => array(array('title' => MODULE_PAYMENT_2CHECKOUT_TEXT_CREDIT_CARD_OWNER,
                                                    'field' => $_POST['pm_2checkout_cc_owner_firstname'] . ' ' . $_POST['pm_2checkout_cc_owner_lastname']),
                                              array('title' => MODULE_PAYMENT_2CHECKOUT_TEXT_CREDIT_CARD_NUMBER,
                                                    'field' => substr($this->cc_card_number, 0, 4) . str_repeat('X', (strlen($this->cc_card_number) - 8)) . substr($this->cc_card_number, -4)),
                                              array('title' => MODULE_PAYMENT_2CHECKOUT_TEXT_CREDIT_CARD_EXPIRES,
                                                    'field' => strftime('%B, %Y', mktime(0,0,0,$_POST['pm_2checkout_cc_expires_month'], 1, '20' . $_POST['pm_2checkout_cc_expires_year'])))));

      return $confirmation;
    }

    function process_button()
    {
      global $order;

      $data['hidden'][0] = array('name' =>x_login, 'value' =>MODULE_PAYMENT_2CHECKOUT_LOGIN);
      $data['hidden'][1] = array('name' =>x_amount, 'value' =>number_format($order->info['total'], 2));
      $data['hidden'][2] = array('name' =>x_invoice_num, 'value' =>date('YmdHis'));
      $data['hidden'][3] = array('name' =>x_test_request, 'value' =>((MODULE_PAYMENT_2CHECKOUT_TESTMODE == 'Test') ? 'Y' : 'N'));
      $data['hidden'][4] = array('name' =>x_card_num, 'value' =>$this->cc_card_number);
      $data['hidden'][5] = array('name' =>cvv, 'value' =>$_POST['pm_2checkout_cc_cvv']);
      $data['hidden'][6] = array('name' =>x_exp_date, 'value' =>$this->cc_expiry_month . substr($this->cc_expiry_year, -2));
      $data['hidden'][7] = array('name' =>x_first_name, 'value' =>$_POST['pm_2checkout_cc_owner_firstname']);
      $data['hidden'][9] = array('name' =x_last_name>, 'value' =>$_POST['pm_2checkout_cc_owner_lastname']);
      $data['hidden'][10] = array('name' =>x_address, 'value' =>$order->customer['street_address']);
      $data['hidden'][11] = array('name' =>x_city, 'value' =>$order->customer['city']);
      $data['hidden'][12] = array('name' =>x_state, 'value' =>$order->customer['state']);
      $data['hidden'][13] = array('name' =>x_zip, 'value' =>$order->customer['postcode']);
      $data['hidden'][14] = array('name' =>x_country, 'value' =>$order->customer['country']['title']);
      $data['hidden'][15] = array('name' =>x_email, 'value' =>$order->customer['email_address']);
      $data['hidden'][16] = array('name' =>x_phone, 'value' =>$order->customer['telephone']);
      $data['hidden'][17] = array('name' =>x_ship_to_first_name, 'value' =>$order->delivery['firstname']);
      $data['hidden'][18] = array('name' =>x_ship_to_last_name, 'value' =>$order->delivery['lastname']);
      $data['hidden'][19] = array('name' =>x_ship_to_address, 'value' =>$order->delivery['street_address']);
      $data['hidden'][20] = array('name' =>x_ship_to_city, 'value' =>$order->delivery['city']);
      $data['hidden'][21] = array('name' =>x_ship_to_state, 'value' =>$order->delivery['state']);
      $data['hidden'][22] = array('name' =>x_ship_to_zip, 'value' =>$order->delivery['postcode']);
      $data['hidden'][23] = array('name' =>x_ship_to_country, 'value' =>$order->delivery['country']['title']);
      $data['hidden'][24] = array('name' =>x_receipt_link_url, 'value' =>xarModURL('commerce','user',(FILENAME_CHECKOUT_PROCESS, '', 'SSL'));
      $data['hidden'][25] = array('name' =>x_email_merchant, 'value' =>((MODULE_PAYMENT_2CHECKOUT_EMAIL_MERCHANT == 'True') ? 'TRUE' : 'FALSE'));
      return $data;
    }

    function before_process()
    {

      if ($_POST['x_response_code'] != '1') {
        xarRedirectResponse(xarModURL('commerce','user',(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(MODULE_PAYMENT_2CHECKOUT_TEXT_ERROR_MESSAGE), 'SSL', true, false));
      }
    }

    function after_process()
    {
      return false;
    }

    function get_error() {

      $error = array('title' => MODULE_PAYMENT_2CHECKOUT_TEXT_ERROR,
                     'error' => stripslashes(urldecode($_GET['error'])));

      return $error;
    }

    function check()
    {
      if (!isset($this->_check)) {
        $check_query = new xenQuery("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_2CHECKOUT_STATUS'");
        $this->_check = $check_query->getrows();
      }
      return $this->_check;
    }

    function install()
    {
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_2CHECKOUT_STATUS', 'True', '6', '0', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_2CHECKOUT_ALLOWED', '',  '6', '0', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_2CHECKOUT_LOGIN', '18157',  '6', '0', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_2CHECKOUT_TESTMODE', 'Test', '6', '0', 'xtc_cfg_select_option(array(\'Test\', \'Production\'), ', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_2CHECKOUT_EMAIL_MERCHANT', 'True',  '6', '0', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_2CHECKOUT_SORT_ORDER', '0',  '6', '0', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_2CHECKOUT_ZONE', '0',  '6', '2', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_2CHECKOUT_ORDER_STATUS_ID', '0',  '6', '0', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
    }

    function remove()
    {
      new xenQuery("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys()
    {
      return array('MODULE_PAYMENT_2CHECKOUT_STATUS','MODULE_PAYMENT_2CHECKOUT_ALLOWED', 'MODULE_PAYMENT_2CHECKOUT_LOGIN', 'MODULE_PAYMENT_2CHECKOUT_TESTMODE', 'MODULE_PAYMENT_2CHECKOUT_EMAIL_MERCHANT', 'MODULE_PAYMENT_2CHECKOUT_ZONE', 'MODULE_PAYMENT_2CHECKOUT_ORDER_STATUS_ID', 'MODULE_PAYMENT_2CHECKOUT_SORT_ORDER');
    }
  }
?>

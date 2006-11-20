<?php
/* -----------------------------------------------------------------------------------------
   $Id: ipayment.php,v 1.1 2003/09/06 22:13:54 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ipayment.php,v 1.32 2003/01/29); www.oscommerce.com
   (c) 2003  nextcommerce (ipayment.php,v 1.9 2003/08/24); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


  class ipayment
  {
    var $code, $title, $description, $enabled;


    function ipayment()
    {
      global $order;

      $this->code = 'ipayment';
      $this->title = MODULE_PAYMENT_IPAYMENT_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_IPAYMENT_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_PAYMENT_IPAYMENT_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_IPAYMENT_STATUS == 'True') ? true : false);

      if ((int)MODULE_PAYMENT_IPAYMENT_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_IPAYMENT_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();

      $this->form_action_url = 'https://ipayment.de/merchant/' . MODULE_PAYMENT_IPAYMENT_ID . '/processor.php';
    }


    function update_status()
    {
      global $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_IPAYMENT_ZONE > 0) ) {
        $check_flag = false;
        $check_query = new xenQuery("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_IPAYMENT_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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
            '    var cc_owner = document.checkout_payment.ipayment_cc_owner.value;' . "\n" .
            '    var cc_number = document.checkout_payment.ipayment_cc_number.value;' . "\n" .
            '    if (cc_owner == "" || cc_owner.length < ' . CC_OWNER_MIN_LENGTH . ') {' . "\n" .
            '      error_message = error_message + "' . MODULE_PAYMENT_IPAYMENT_TEXT_JS_CC_OWNER . '";' . "\n" .
            '      error = 1;' . "\n" .
            '    }' . "\n" .
            '    if (cc_number == "" || cc_number.length < ' . CC_NUMBER_MIN_LENGTH . ') {' . "\n" .
            '      error_message = error_message + "' . MODULE_PAYMENT_IPAYMENT_TEXT_JS_CC_NUMBER . '";' . "\n" .
            '      error = 1;' . "\n" .
            '    }' . "\n" .
            '  }' . "\n";

      return $js;
    }

    function selection()
    {
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
                         'fields' => array(array('title' => MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_OWNER,
                                                 'field' => xtc_draw_input_field('ipayment_cc_owner', $order->billing['firstname'] . ' ' . $order->billing['lastname'])),
                                           array('title' => MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_NUMBER,
                                                 'field' => xtc_draw_input_field('ipayment_cc_number')),
                                           array('title' => MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_EXPIRES,
                                                 'field' => commerce_userapi_draw_pull_down_menu('ipayment_cc_expires_month', $expires_month) . '&#160;' . commerce_userapi_draw_pull_down_menu('ipayment_cc_expires_year', $expires_year)),
                                           array('title' => MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_CHECKNUMBER,
                                                 'field' => xtc_draw_input_field('ipayment_cc_checkcode', '', 'size="4" maxlength="3"') . '&#160;<small>' . MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_CHECKNUMBER_LOCATION . '</small>')));

      return $selection;
    }

    function pre_confirmation_check()
    {

      include(DIR_WS_CLASSES . 'cc_validation.php');

      $cc_validation = new cc_validation();
      $result = $cc_validation->validate($_POST['ipayment_cc_number'], $_POST['ipayment_cc_expires_month'], $_POST['ipayment_cc_expires_year']);

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
        $payment_error_return = 'payment_error=' . $this->code . '&error=' . urlencode($error) . '&ipayment_cc_owner=' . urlencode($_POST['ipayment_cc_owner']) . '&ipayment_cc_expires_month=' . $_POST['ipayment_cc_expires_month'] . '&ipayment_cc_expires_year=' . $_POST['ipayment_cc_expires_year'] . '&ipayment_cc_checkcode=' . $_POST['ipayment_cc_checkcode'];

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
                            'fields' => array(array('title' => MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_OWNER,
                                                    'field' => $_POST['ipayment_cc_owner']),
                                              array('title' => MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_NUMBER,
                                                    'field' => substr($this->cc_card_number, 0, 4) . str_repeat('X', (strlen($this->cc_card_number) - 8)) . substr($this->cc_card_number, -4)),
                                              array('title' => MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_EXPIRES,
                                                    'field' => strftime('%B, %Y', mktime(0,0,0,$_POST['ipayment_cc_expires_month'], 1, '20' . $_POST['ipayment_cc_expires_year'])))));

      if (xarModAPIFunc('commerce','user','not_null',array('arg' => $_POST['ipayment_cc_checkcode']))) {
        $confirmation['fields'][] = array('title' => MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_CHECKNUMBER,
                                          'field' => $_POST['ipayment_cc_checkcode']);
      }

      return $confirmation;
    }

    function process_button()
    {
      global $order, $currencies;

      switch (MODULE_PAYMENT_IPAYMENT_CURRENCY) {
        case 'Always EUR':
          $trx_currency = 'EUR';
          break;
        case 'Always USD':
          $trx_currency = 'USD';
          break;
        case 'Either EUR or USD, else EUR':
          if ( ($_SESSION['currency'] == 'EUR') || ($_SESSION['currency'] == 'USD') ) {
            $trx_currency = $_SESSION['currency'];
          } else {
            $trx_currency = 'EUR';
          }
          break;
        case 'Either EUR or USD, else USD':
          if ( ($_SESSION['currency'] == 'EUR') || ($_SESSION['currency'] == 'USD') ) {
            $trx_currency = $_SESSION['currency'];
          } else {
            $trx_currency = 'USD';
          }
          break;
      }

      $data['hidden'][0] = array('name' =>silent, 'value' =>1);
      $data['hidden'][1] = array('name' =>trx_paymenttyp, 'value' =>cc);
      $data['hidden'][2] = array('name' =>trxuser_id, 'value' =>MODULE_PAYMENT_IPAYMENT_USER_ID);
      $data['hidden'][3] = array('name' =>trxpassword, 'value' =>MODULE_PAYMENT_IPAYMENT_PASSWORD);
      $data['hidden'][4] = array('name' =>item_name, 'value' =>STORE_NAME);
      $data['hidden'][5] = array('name' =>trx_currency, 'value' =>$trx_currency);
      $data['hidden'][6] = array('name' =>trx_amount, 'value' =>number_format($order->info['total'] * 100 * $currencies->get_value($trx_currency), 0, '',''));
      $data['hidden'][7] = array('name' =>cc_expdate_month, 'value' =>$_POST['ipayment_cc_expires_month']) .
                               xtc_draw_hidden_field('cc_expdate_year', $_POST['ipayment_cc_expires_year']);
      $data['hidden'][8] = array('name' =>cc_number, 'value' =>$_POST['ipayment_cc_number']);
      $data['hidden'][9] = array('name' =>cc_checkcode, 'value' =>$_POST['ipayment_cc_checkcode']);
      $data['hidden'][10] = array('name' =>addr_name, 'value' =>$_POST['ipayment_cc_owner']);
      $data['hidden'][11] = array('name' =>addr_email, 'value' =>$order->customer['email_address']);
      $data['hidden'][12] = array('name' =>redirect_url, 'value' =>xarModURL('commerce','user',(FILENAME_CHECKOUT_PROCESS, '', 'SSL', true));
      $data['hidden'][13] = array('name' =>silent_error_url, 'value' =>xarModURL('commerce','user',(FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $this->code . '&ipayment_cc_owner=' . urlencode($_POST['ipayment_cc_owner']), 'SSL', true));

      return $data;
    }

    function before_process()
    {
      return false;
    }

    function after_process()
    {
      return false;
    }

    function get_error()
    {

      $error = array('title' => IPAYMENT_ERROR_HEADING,
                     'error' => ((isset($_GET['error'])) ? stripslashes(urldecode($_GET['error'])) : IPAYMENT_ERROR_MESSAGE));

      return $error;
    }

    function check()
    {
      if (!isset($this->_check)) {
        $check_query = new xenQuery("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_IPAYMENT_STATUS'");
        $this->_check = $check_query->getrows();
      }
      return $this->_check;
    }

    function install()
    {
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_IPAYMENT_STATUS', 'True', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_IPAYMENT_ALLOWED', '', '6', '0', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_IPAYMENT_ID', '99999', '6', '2', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_IPAYMENT_USER_ID', '99999', '6', '3', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_IPAYMENT_PASSWORD', '0', '6', '4', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_IPAYMENT_CURRENCY', 'Either EUR or USD, else EUR','6', '5', 'xtc_cfg_select_option(array(\'Always EUR\', \'Always USD\', \'Either EUR or USD, else EUR\', \'Either EUR or USD, else USD\'), ', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_IPAYMENT_SORT_ORDER', '0', '6', '0', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_IPAYMENT_ZONE', '0', '6', '2', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_IPAYMENT_ORDER_STATUS_ID', '0','6', '0', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
    }

    function remove()
    {
      new xenQuery("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys()
    {
      return array('MODULE_PAYMENT_IPAYMENT_STATUS','MODULE_PAYMENT_IPAYMENT_ALLOWED', 'MODULE_PAYMENT_IPAYMENT_ID', 'MODULE_PAYMENT_IPAYMENT_USER_ID', 'MODULE_PAYMENT_IPAYMENT_PASSWORD', 'MODULE_PAYMENT_IPAYMENT_CURRENCY', 'MODULE_PAYMENT_IPAYMENT_ZONE', 'MODULE_PAYMENT_IPAYMENT_ORDER_STATUS_ID', 'MODULE_PAYMENT_IPAYMENT_SORT_ORDER');
    }
  }
?>

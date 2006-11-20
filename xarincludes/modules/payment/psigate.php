<?php
/* -----------------------------------------------------------------------------------------
   $Id: psigate.php,v 1.1 2003/09/06 22:13:54 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(psigate.php,v 1.16 2003/01/29); www.oscommerce.com
   (c) 2003  nextcommerce (psigate.php,v 1.8 2003/08/24); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


  class psigate
  {
    var $code, $title, $description, $enabled;


    function psigate()
    {
      global $order;

      $this->code = 'psigate';
      $this->title = MODULE_PAYMENT_PSIGATE_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_PSIGATE_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_PAYMENT_PSIGATE_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_PSIGATE_STATUS == 'True') ? true : false);

      if ((int)MODULE_PAYMENT_PSIGATE_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_PSIGATE_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();

      $this->form_action_url = 'https://order.psigate.com/psigate.asp';
    }


    function update_status()
    {
      global $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_PSIGATE_ZONE > 0) ) {
        $check_flag = false;
        $check_query = new xenQuery("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_PSIGATE_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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
      if (MODULE_PAYMENT_PSIGATE_INPUT_MODE == 'Local') {
        $js = 'if (payment_value == "' . $this->code . '") {' . "\n" .
              '  var psigate_cc_number = document.checkout_payment.psigate_cc_number.value;' . "\n" .
              '  if (psigate_cc_number == "" || psigate_cc_number.length < ' . CC_NUMBER_MIN_LENGTH . ') {' . "\n" .
              '    error_message = error_message + "' . MODULE_PAYMENT_PSIGATE_TEXT_JS_CC_NUMBER . '";' . "\n" .
              '    error = 1;' . "\n" .
              '  }' . "\n" .
              '}' . "\n";

        return $js;
      } else {
        return false;
      }
    }

    function selection()
    {
      global $order;

      if (MODULE_PAYMENT_PSIGATE_INPUT_MODE == 'Local') {
        for ($i=1; $i<13; $i++) {
          $expires_month[] = array('id' => sprintf('%02d', $i), 'text' => strftime('%B',mktime(0,0,0,$i,1,2000)));
        }

        $today = getdate();
        for ($i=$today['year']; $i < $today['year']+10; $i++) {
          $expires_year[] = array('id' => strftime('%y',mktime(0,0,0,1,1,$i)), 'text' => strftime('%Y',mktime(0,0,0,1,1,$i)));
        }

        $selection = array('id' => $this->code,
                           'module' => $this->title,
                           'fields' => array(array('title' => MODULE_PAYMENT_PSIGATE_TEXT_CREDIT_CARD_OWNER,
                                                   'field' => $order->billing['firstname'] . ' ' . $order->billing['lastname']),
                                             array('title' => MODULE_PAYMENT_PSIGATE_TEXT_CREDIT_CARD_NUMBER,
                                                   'field' => xtc_draw_input_field('psigate_cc_number')),
                                             array('title' => MODULE_PAYMENT_PSIGATE_TEXT_CREDIT_CARD_EXPIRES,
                                                   'field' => commerce_userapi_draw_pull_down_menu('psigate_cc_expires_month', $expires_month) . '&#160;' . commerce_userapi_draw_pull_down_menu('psigate_cc_expires_year', $expires_year))));
      } else {
        $selection = array('id' => $this->code,
                           'module' => $this->title);
      }

      return $selection;
    }

    function pre_confirmation_check()
    {

      if (MODULE_PAYMENT_PSIGATE_INPUT_MODE == 'Local') {
        include(DIR_WS_CLASSES . 'cc_validation.php');

        $cc_validation = new cc_validation();
        $result = $cc_validation->validate($_POST['psigate_cc_number'], $_POST['psigate_cc_expires_month'], $_POST['psigate_cc_expires_year']);

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
          $payment_error_return = 'payment_error=' . $this->code . '&error=' . urlencode($error) . '&psigate_cc_owner=' . urlencode($_POST['psigate_cc_owner']) . '&psigate_cc_expires_month=' . $_POST['psigate_cc_expires_month'] . '&psigate_cc_expires_year=' . $_POST['psigate_cc_expires_year'];

          xarRedirectResponse(xarModURL('commerce','user',(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
        }

        $this->cc_card_type = $cc_validation->cc_type;
        $this->cc_card_number = $cc_validation->cc_number;
        $this->cc_expiry_month = $cc_validation->cc_expiry_month;
        $this->cc_expiry_year = $cc_validation->cc_expiry_year;
      } else {
        return false;
      }
    }

    function confirmation()
    {
      global $order;

      if (MODULE_PAYMENT_PSIGATE_INPUT_MODE == 'Local') {
        $confirmation = array('title' => $this->title . ': ' . $this->cc_card_type,
                              'fields' => array(array('title' => MODULE_PAYMENT_PSIGATE_TEXT_CREDIT_CARD_OWNER,
                                                      'field' => $order->billing['firstname'] . ' ' . $order->billing['lastname']),
                                                array('title' => MODULE_PAYMENT_PSIGATE_TEXT_CREDIT_CARD_NUMBER,
                                                      'field' => substr($this->cc_card_number, 0, 4) . str_repeat('X', (strlen($this->cc_card_number) - 8)) . substr($this->cc_card_number, -4)),
                                                array('title' => MODULE_PAYMENT_PSIGATE_TEXT_CREDIT_CARD_EXPIRES,
                                                      'field' => strftime('%B, %Y', mktime(0,0,0,$_POST['psigate_cc_expires_month'], 1, '20' . $_POST['psigate_cc_expires_year'])))));

        return $confirmation;
      } else {
        return false;
      }
    }

    function process_button()
    {
      global $order, $currencies;

      switch (MODULE_PAYMENT_PSIGATE_TRANSACTION_MODE) {
        case 'Always Good':
          $transaction_mode = '1';
          break;
        case 'Always Duplicate':
          $transaction_mode = '2';
          break;
        case 'Always Decline':
          $transaction_mode = '3';
          break;
        case 'Production':
        default:
          $transaction_mode = '0';
          break;
      }

      switch (MODULE_PAYMENT_PSIGATE_TRANSACTION_TYPE) {
        case 'Sale':
          $transaction_type = '0';
          break;
        case 'PostAuth':
          $transaction_type = '2';
          break;
        case 'PreAuth':
        default:
          $transaction_type = '1';
          break;
      }

      $data['hidden'][0] = array('name' =>MerchantID, 'value' =>MODULE_PAYMENT_PSIGATE_MERCHANT_ID);
      $data['hidden'][1] = array('name' =>FullTotal, 'value' =>number_format($order->info['total'] * $currencies->get_value(MODULE_PAYMENT_PSIGATE_CURRENCY), $currencies->currencies[MODULE_PAYMENT_PSIGATE_CURRENCY]['decimal_places']));
      $data['hidden'][2] = array('name' =>ThanksURL, 'value' =>xarModURL('commerce','user',(FILENAME_CHECKOUT_PROCESS, '', 'SSL', true));
      $data['hidden'][3] = array('name' =>NoThanksURL, 'value' =>xarModURL('commerce','user',(FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $this->code, 'NONSSL', true));
      $data['hidden'][4] = array('name' =>Bname, 'value' =>$order->billing['firstname'] . ' ' . $order->billing['lastname']);
      $data['hidden'][5] = array('name' =>Baddr1, 'value' =>$order->billing['street_address']);
      $data['hidden'][6] = array('name' =>Bcity, 'value' =>$order->billing['city']);
      $data['hidden'][7] = array('name' =>Bstate, 'value' =>$order->billing['state']);
      $data['hidden'][8] = array('name' =>Bzip, 'value' =>$order->billing['postcode']);
      $data['hidden'][9] = array('name' =>Bcountry, 'value' =>$order->billing['country']['iso_code_2']);
      $data['hidden'][10] = array('name' =>Phone, 'value' =>$order->customer['telephone']);
      $data['hidden'][11] = array('name' =>Email, 'value' =>$order->customer['email_address']);
      $data['hidden'][12] = array('name' =>Sname, 'value' =>$order->delivery['firstname'] . ' ' . $order->delivery['lastname']);
      $data['hidden'][13] = array('name' =>Saddr1, 'value' =>$order->delivery['street_address']);
      $data['hidden'][14] = array('name' =>Scity, 'value' =>$order->delivery['city']);
      $data['hidden'][15] = array('name' =>Sstate, 'value' =>$order->delivery['state']);
      $data['hidden'][16] = array('name' =>Szip, 'value' =>$order->delivery['postcode']);
      $data['hidden'][17] = array('name' =>Scountry, 'value' =>$order->delivery['country']['iso_code_2']);
      $data['hidden'][18] = array('name' =>ChargeType, 'value' =>$transaction_type);
      $data['hidden'][19] = array('name' =>Result, 'value' =>$transaction_mode);
      $data['hidden'][20] = array('name' =>IP, 'value' =>$_SERVER['REMOTE_ADDR']);

      if (MODULE_PAYMENT_PSIGATE_INPUT_MODE == 'Local') {
          $data['hidden'][21] = array('name' =>CardNumber, 'value' =>$this->cc_card_number);
          $data['hidden'][22] = array('name' =>ExpMonth, 'value' =>$this->cc_expiry_month);
          $data['hidden'][23] = array('name' =>ExpYear, 'value' =>substr($this->cc_expiry_year, -2));
      }

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

      if (isset($_GET['ErrMsg']) && (strlen($_GET['ErrMsg']) > 0)) {
        $error = stripslashes(urldecode($_GET['ErrMsg']));
      } elseif (isset($_GET['error']) && (strlen($_GET['error']) > 0)) {
        $error = stripslashes(urldecode($_GET['error']));
      } else {
        $error = MODULE_PAYMENT_PSIGATE_TEXT_ERROR_MESSAGE;
      }

      return array('title' => MODULE_PAYMENT_PSIGATE_TEXT_ERROR,
                   'error' => $error);
    }

    function check()
    {
      if (!isset($this->_check)) {
        $check_query = new xenQuery("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_PSIGATE_STATUS'");
        $this->_check = $check_query->getrows();
      }
      return $this->_check;
    }

    function install()
    {
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_PSIGATE_STATUS', 'True','6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_PSIGATE_ALLOWED', '', '6', '0', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_PSIGATE_MERCHANT_ID', 'teststorewithcard', '6', '2', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_PSIGATE_TRANSACTION_MODE', 'Always Good',  '6', '3', 'xtc_cfg_select_option(array(\'Production\', \'Always Good\', \'Always Duplicate\', \'Always Decline\'), ', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_PSIGATE_TRANSACTION_TYPE', 'PreAuth',  '6', '4', 'xtc_cfg_select_option(array(\'Sale\', \'PreAuth\', \'PostAuth\'), ', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_PSIGATE_INPUT_MODE', 'Local', '6', '5', 'xtc_cfg_select_option(array(\'Local\', \'Remote\'), ', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_PSIGATE_CURRENCY', 'USD', '6', '6', 'xtc_cfg_select_option(array(\'CAD\', \'USD\'), ', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_PSIGATE_SORT_ORDER', '0', '6', '0', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_PSIGATE_ZONE', '0', '6', '2', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_PSIGATE_ORDER_STATUS_ID', '0', '6', '0', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
    }

    function remove()
    {
      new xenQuery("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys()
    {
      return array('MODULE_PAYMENT_PSIGATE_STATUS','MODULE_PAYMENT_PSIGATE_ALLOWED', 'MODULE_PAYMENT_PSIGATE_MERCHANT_ID', 'MODULE_PAYMENT_PSIGATE_TRANSACTION_MODE', 'MODULE_PAYMENT_PSIGATE_TRANSACTION_TYPE', 'MODULE_PAYMENT_PSIGATE_INPUT_MODE', 'MODULE_PAYMENT_PSIGATE_CURRENCY', 'MODULE_PAYMENT_PSIGATE_ZONE', 'MODULE_PAYMENT_PSIGATE_ORDER_STATUS_ID', 'MODULE_PAYMENT_PSIGATE_SORT_ORDER');
    }
  }
?>

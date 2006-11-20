<?php
/* -----------------------------------------------------------------------------------------
   $Id: secpay.php,v 1.1 2003/09/06 22:13:54 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(secpay.php,v 1.31 2003/01/29); www.oscommerce.com
   (c) 2003  nextcommerce (secpay.php,v 1.8 2003/08/24); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


  class secpay
  {
    var $code, $title, $description, $enabled;


    function secpay()
    {
      global $order;

      $this->code = 'secpay';
      $this->title = MODULE_PAYMENT_SECPAY_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_SECPAY_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_PAYMENT_SECPAY_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_SECPAY_STATUS == 'True') ? true : false);

      if ((int)MODULE_PAYMENT_SECPAY_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_SECPAY_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();

      $this->form_action_url = 'https://www.secpay.com/java-bin/ValCard';
    }


    function update_status()
    {
      global $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_SECPAY_ZONE > 0) ) {
        $check_flag = false;
        $check_query = new xenQuery("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_SECPAY_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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
      return false;
    }

    function selection()
    {
      return array('id' => $this->code,
                   'module' => $this->title);
    }

    function pre_confirmation_check()
    {
      return false;
    }

    function confirmation()
    {
      return false;
    }

    function process_button()
    {
      global $order, $currencies;

      switch (MODULE_PAYMENT_SECPAY_CURRENCY) {
        case 'Default Currency':
          $sec_currency = DEFAULT_CURRENCY;
          break;
        case 'Any Currency':
        default:
          $sec_currency = $_SESSION['currency'];
          break;
      }

      switch (MODULE_PAYMENT_SECPAY_TEST_STATUS) {
        case 'Always Fail':
          $test_status = 'false';
          break;
        case 'Production':
          $test_status = 'live';
          break;
        case 'Always Successful':
        default:
          $test_status = 'true';
          break;
      }

      $data['hidden'][0] = array('name' =>merchant, 'value' =>MODULE_PAYMENT_SECPAY_MERCHANT_ID);
      $data['hidden'][1] = array('name' =>trans_id, 'value' =>STORE_NAME . date('Ymdhis'));
      $data['hidden'][2] = array('name' =>amount, 'value' =>number_format($order->info['total'] * $currencies->get_value($sec_currency), $currencies->currencies[$sec_currency]['decimal_places'], '.', ''));
      $data['hidden'][3] = array('name' =>bill_name, 'value' =>$order->billing['firstname'] . ' ' . $order->billing['lastname']);
      $data['hidden'][4] = array('name' =>bill_addr_1, 'value' =>$order->billing['street_address']);
      $data['hidden'][5] = array('name' =>bill_addr_2, 'value' =>$order->billing['suburb']);
      $data['hidden'][6] = array('name' =>bill_city, 'value' =>$order->billing['city']);
      $data['hidden'][7] = array('name' =>bill_state, 'value' =>$order->billing['state']);
      $data['hidden'][8] = array('name' =>bill_post_code, 'value' =>$order->billing['postcode']);
      $data['hidden'][9] = array('name' =>bill_country, 'value' =>$order->billing['country']['title']);
      $data['hidden'][10] = array('name' =>bill_tel, 'value' =>$order->customer['telephone']);
      $data['hidden'][11] = array('name' =>bill_email, 'value' =>$order->customer['email_address']);
      $data['hidden'][12] = array('name' =>ship_name, 'value' =>$order->delivery['firstname'] . ' ' . $order->delivery['lastname']);
      $data['hidden'][13] = array('name' =>ship_addr_1, 'value' =>$order->delivery['street_address']);
      $data['hidden'][14] = array('name' =>ship_addr_2, 'value' =>$order->delivery['suburb']);
      $data['hidden'][15] = array('name' =>ship_city, 'value' =>$order->delivery['city']);
      $data['hidden'][16] = array('name' =>ship_state, 'value' =>$order->delivery['state']);
      $data['hidden'][17] = array('name' =>ship_post_code, 'value' =>$order->delivery['postcode']);
      $data['hidden'][18] = array('name' =>ship_country, 'value' =>$order->delivery['country']['title']);
      $data['hidden'][19] = array('name' =>currency, 'value' =>$sec_currency);
      $data['hidden'][20] = array('name' =>callback, 'value' =>xarModURL('commerce','user',(FILENAME_CHECKOUT_PROCESS, '', 'SSL', false) . ';' . xarModURL('commerce','user',(FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $this->code, 'SSL', false));
      $data['hidden'][21] = array('name' =>xtc_session_name(), 'value' =>xtc_session_id());
      $data['hidden'][22] = array('name' =>options, 'value' =>);
      $data['hidden'][23] = array('name' =>, 'value' =>test_status=' . $test_status . ',dups=false,cb_post=true,cb_flds=' . xtc_session_name());

      return $data;
    }

    function before_process()
    {

      if ($_POST['valid'] == 'true') {
        if ($remote_host = getenv('REMOTE_HOST')) {
          if ($remote_host != 'secpay.com') {
            $remote_host = gethostbyaddr($remote_host);
          }
          if ($remote_host != 'secpay.com') {
            xarRedirectResponse(xarModURL('commerce','user',(FILENAME_CHECKOUT_PAYMENT, xtc_session_name() . '=' . $_POST[xtc_session_name()] . '&payment_error=' . $this->code, 'SSL', false, false));
          }
        } else {
          xarRedirectResponse(xarModURL('commerce','user',(FILENAME_CHECKOUT_PAYMENT, xtc_session_name() . '=' . $_POST[xtc_session_name()] . '&payment_error=' . $this->code, 'SSL', false, false));
        }
      }
    }

    function after_process()
    {
      return false;
    }

    function get_error()
    {

      if (isset($_GET['message']) && (strlen($_GET['message']) > 0)) {
        $error = stripslashes(urldecode($_GET['message']));
      } else {
        $error = MODULE_PAYMENT_SECPAY_TEXT_ERROR_MESSAGE;
      }

      return array('title' => MODULE_PAYMENT_SECPAY_TEXT_ERROR,
                   'error' => $error);
    }

    function check() {
      if (!isset($this->_check))
      {
        $check_query = new xenQuery("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_SECPAY_STATUS'");
        $this->_check = $check_query->getrows();
      }
      return $this->_check;
    }

    function install()
    {
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_SECPAY_STATUS', 'True', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SECPAY_ALLOWED', '', '6', '0', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SECPAY_MERCHANT_ID', 'secpay',  '6', '2', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_SECPAY_CURRENCY', 'Any Currency',  '6', '3', 'xtc_cfg_select_option(array(\'Any Currency\', \'Default Currency\'), ', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_SECPAY_TEST_STATUS', 'Always Successful','6', '4', 'xtc_cfg_select_option(array(\'Always Successful\', \'Always Fail\', \'Production\'), ', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SECPAY_SORT_ORDER', '0',  '6', '0', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_SECPAY_ZONE', '0',  '6', '2', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_SECPAY_ORDER_STATUS_ID', '0',  '6', '0', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
    }

    function remove()
    {
      new xenQuery("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys()
    {
      return array('MODULE_PAYMENT_SECPAY_STATUS','MODULE_PAYMENT_SECPAY_ALLOWED', 'MODULE_PAYMENT_SECPAY_MERCHANT_ID', 'MODULE_PAYMENT_SECPAY_CURRENCY', 'MODULE_PAYMENT_SECPAY_TEST_STATUS', 'MODULE_PAYMENT_SECPAY_ZONE', 'MODULE_PAYMENT_SECPAY_ORDER_STATUS_ID', 'MODULE_PAYMENT_SECPAY_SORT_ORDER');
    }
  }
?>

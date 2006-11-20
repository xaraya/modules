<?php
/* -----------------------------------------------------------------------------------------
   $Id: nochex.php,v 1.1 2003/09/06 22:13:54 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(nochex.php,v 1.12 2003/01/29); www.oscommerce.com
   (c) 2003  nextcommerce (nochex.php,v 1.8 2003/08/24); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


  class nochex
  {
    var $code, $title, $description, $enabled;


    function nochex()
    {
      global $order;

      $this->code = 'nochex';
      $this->title = MODULE_PAYMENT_NOCHEX_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_NOCHEX_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_PAYMENT_NOCHEX_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_NOCHEX_STATUS == 'True') ? true : false);

      if ((int)MODULE_PAYMENT_NOCHEX_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_NOCHEX_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();

      $this->form_action_url = 'https://www.nochex.com/nochex.dll/checkout';
    }


    function update_status()
    {
      global $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_NOCHEX_ZONE > 0) ) {
        $check_flag = false;
        $check_query = new xenQuery("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_NOCHEX_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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

      $data['hidden'][0] = array('name' =>cmd, 'value' =>_xclick);
      $data['hidden'][1] = array('name' =>email, 'value' =>MODULE_PAYMENT_NOCHEX_ID);
      $data['hidden'][2] = array('name' =>amount, 'value' =>number_format($order->info['total'] * $currencies->currencies['GBP']['value'], $currencies->currencies['GBP']['decimal_places']));
      $data['hidden'][3] = array('name' =>ordernumber, 'value' =>$_SESSION['customer_id'] . '-' . date('Ymdhis'));
      $data['hidden'][4] = array('name' =>returnurl, 'value' =>xarModURL('commerce','user',(FILENAME_CHECKOUT_PROCESS, '', 'SSL'));
      $data['hidden'][5] = array('name' =>cancel_return, 'value' =>xarModURL('commerce','user',(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));

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

    function output_error()
    {
      return false;
    }

    function check()
    {
      if (!isset($this->_check)) {
        $check_query = new xenQuery("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_NOCHEX_STATUS'");
        $this->_check = $check_query->getrows();
      }
      return $this->_check;
    }

    function install()
    {
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_NOCHEX_STATUS', 'True', '6', '3', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOCHEX_ALLOWED', '', '6', '0', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOCHEX_ID', 'you@yourbuisness.com', '6', '4', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOCHEX_SORT_ORDER', '0', '6', '0', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_NOCHEX_ZONE', '0',  '6', '2', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_NOCHEX_ORDER_STATUS_ID', '0', '6', '0', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
    }

    function remove()
    {
      new xenQuery("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys()
    {
      return array('MODULE_PAYMENT_NOCHEX_STATUS','MODULE_PAYMENT_NOCHEX_ALLOWED', 'MODULE_PAYMENT_NOCHEX_ID', 'MODULE_PAYMENT_NOCHEX_ZONE', 'MODULE_PAYMENT_NOCHEX_ORDER_STATUS_ID', 'MODULE_PAYMENT_NOCHEX_SORT_ORDER');
    }
  }
?>

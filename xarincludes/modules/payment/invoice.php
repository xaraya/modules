<?php
/* -----------------------------------------------------------------------------------------
   $Id: invoice.php,v 1.1 2003/09/06 22:13:54 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(cod.php,v 1.28 2003/02/14); www.oscommerce.com
   (c) 2003  nextcommerce (invoice.php,v 1.6 2003/08/24); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


  class invoice
  {
    var $code, $title, $description, $enabled;


    function invoice()
    {
      global $order;

      $this->code = 'invoice';
      $this->title = MODULE_PAYMENT_INVOICE_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_INVOICE_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_PAYMENT_INVOICE_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_INVOICE_STATUS == 'True') ? true : false);

      if ((int)MODULE_PAYMENT_INVOICE_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_INVOICE_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();
    }


    function update_status()
    {
      global $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_INVOICE_ZONE > 0) ) {
        $check_flag = false;
        $check_query = new xenQuery("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_INVOICE_ZONE . "' and zone_country_id = '" . $order->delivery['country']['id'] . "' order by zone_id");
      $q = new xenQuery();
      if(!$q->run()) return;
        while ($check = $q->output()) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check['zone_id'] == $order->delivery['zone_id']) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag == false) {
          $this->enabled = false;
        }
      }

      if ($this->enabled == true) {
        if ($order->content_type == 'virtual') {
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
      return false;
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
      return false;
    }

    function check()
    {
      if (!isset($this->_check)) {
        $check_query = new xenQuery("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_INVOICE_STATUS'");
        $this->_check = $check_query->getrows();
      }
      return $this->_check;
    }

    function install()
    {
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_INVOICE_STATUS', 'True',  '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_INVOICE_ALLOWED', '', '6', '0', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_INVOICE_ZONE', '0',  '6', '2', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_INVOICE_SORT_ORDER', '0',  '6', '0', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_INVOICE_ORDER_STATUS_ID', '0',  '6', '0', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
   }

    function remove()
    {
      new xenQuery("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys()
    {
      return array('MODULE_PAYMENT_INVOICE_STATUS','MODULE_PAYMENT_INVOICE_ALLOWED', 'MODULE_PAYMENT_INVOICE_ZONE', 'MODULE_PAYMENT_INVOICE_ORDER_STATUS_ID', 'MODULE_PAYMENT_INVOICE_SORT_ORDER');
    }
  }
?>

<?php
/* -----------------------------------------------------------------------------------------
   $Id: ot_subtotal_no_tax.php,v 1.1 2003/09/06 22:13:54 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ot_subtotal.php,v 1.7 2003/02/13); www.oscommerce.com
   (c) 2003  nextcommerce (ot_subtotal_no_tax.php,v 1.7 2003/08/24); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


  class ot_subtotal_no_tax {

    var $title, $output;

    function ot_subtotal_no_tax() {
      $this->code = 'ot_subtotal_no_tax';
      $this->title = MODULE_ORDER_TOTAL_SUBTOTAL_NO_TAX_TITLE;
      $this->description = MODULE_ORDER_TOTAL_SUBTOTAL_NO_TAX_DESCRIPTION;
      $this->enabled = ((MODULE_ORDER_TOTAL_SUBTOTAL_NO_TAX_STATUS == 'true') ? true : false);
      $this->sort_order = MODULE_ORDER_TOTAL_SUBTOTAL_NO_TAX_SORT_ORDER;

      $this->output = array();
    }

    function process() {
      global $order, $currencies;

      if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
        if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1) {
          $sub_total_price = $order->info['subtotal'] - ($order->info['subtotal'] / 100 * $_SESSION['customers_status']['customers_status_ot_discount']);
    } else {
      $sub_total_price = $order->info['subtotal'];
    }
        $this->output[] = array('title' => $this->title . ':',
                                'text' => '<b>' . xtc_format_price($sub_total_price+(xtc_format_price($order->info['shipping_cost'], $price_special=0, $calculate_currencies=true)), $price_special=1, $calculate_currencies=false).'</b>',
                                'value' => xtc_format_price($sub_total_price+(xtc_format_price($order->info['shipping_cost'], $price_special=0, $calculate_currencies=true)), $price_special=0, $calculate_currencies=false));
      }
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = new xenQuery("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_SUBTOTAL_NO_TAX_STATUS'");
        $this->_check = $check_query->getrows();
      }

      return $this->_check;
    }

    function keys() {
      return array('MODULE_ORDER_TOTAL_SUBTOTAL_NO_TAX_STATUS', 'MODULE_ORDER_TOTAL_SUBTOTAL_NO_TAX_SORT_ORDER');
    }

    function install() {
      new xenQuery("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_SUBTOTAL_NO_TAX_STATUS', 'true', '6', '1','xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_SUBTOTAL_NO_TAX_SORT_ORDER', '4','6', '2', now())");
    }

    function remove() {
      new xenQuery("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }
  }
?>
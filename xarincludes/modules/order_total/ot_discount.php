<?php
/* -----------------------------------------------------------------------------------------
   $Id: ot_discount.php,v 1.2 2003/10/01 18:17:10 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ot_subtotal.php,v 1.7 2003/02/13); www.oscommerce.com
   (c) 2003  nextcommerce (ot_discount.php,v 1.11 2003/08/24); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  class ot_discount {
    var $title, $output;

    function ot_discount() {
      $this->code = 'ot_discount';
      $this->title = MODULE_ORDER_TOTAL_DISCOUNT_TITLE;
      $this->description = MODULE_ORDER_TOTAL_DISCOUNT_DESCRIPTION;
      $this->enabled = ((MODULE_ORDER_TOTAL_DISCOUNT_STATUS == 'true') ? true : false);
      $this->sort_order = MODULE_ORDER_TOTAL_DISCOUNT_SORT_ORDER;

      $this->output = array();
    }

    function process() {
      global $order, $currencies;
      $this->title = $_SESSION['customers_status']['customers_status_ot_discount'] . ' % ' . SUB_TITLE_OT_DISCOUNT;
      if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == '1' && $_SESSION['customers_status']['customers_status_ot_discount']!='0.00') {
        $discount_price = xtc_format_price($order->info['subtotal'], $price_special=0, $calculate_currencies=false) / 100 * $_SESSION['customers_status']['customers_status_ot_discount']*-1;
        $this->output[] = array('title' => $this->title . ':',
                                'text' => '<font color="ff0000">'.xtc_format_price($discount_price,$price_special=1,$calculate_currencies=false).'</font>',
                                'value' => $discount_price);
      }
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = new xenQuery("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_DISCOUNT_STATUS'");
        $this->_check = $check_query->getrows();
      }

      return $this->_check;
    }

    function keys() {
      return array('MODULE_ORDER_TOTAL_DISCOUNT_STATUS', 'MODULE_ORDER_TOTAL_DISCOUNT_SORT_ORDER');
    }

    function install() {
      new xenQuery("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_DISCOUNT_STATUS', 'true','6', '1','xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
      new xenQuery("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_DISCOUNT_SORT_ORDER', '2', '7', '2', now())");
    }

    function remove() {
      new xenQuery("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }
  }
?>
<?php
/* -----------------------------------------------------------------------------------------
   $Id: ot_cod_fee.php,v 1.1 2003/10/01 18:17:10 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ot_cod_fee.php,v 1.02 2003/02/24); www.oscommerce.com
   (C) 2001 - 2003 TheMedia, Dipl.-Ing Thomas Plänkers ; http://www.themedia.at & http://www.oscommerce.at

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:

   Adapted for xtcommerce 2003/09/30 by Benax (axel.benkert@online-power.de)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


  class ot_cod_fee {
    var $title, $output;

    function ot_cod_fee() {
      $this->code = 'ot_cod_fee';
      $this->title = MODULE_ORDER_TOTAL_COD_TITLE;
      $this->description = MODULE_ORDER_TOTAL_COD_DESCRIPTION;
      $this->enabled = ((MODULE_ORDER_TOTAL_COD_STATUS == 'true') ? true : false);
      $this->sort_order = MODULE_ORDER_TOTAL_COD_SORT_ORDER;

      $this->output = array();
    }

    function process() {
      global $order, $currencies, $cod_cost, $cod_country, $shipping;

      if (MODULE_ORDER_TOTAL_COD_STATUS == 'true') {

        //Will become true, if cod can be processed.
        $cod_country = false;

        //check if payment method is cod. If yes, check if cod is possible.
        if ($_SESSION['payment'] == 'cod') {
          //process installed shipping modules
          if ($_SESSION['shipping']['id'] == 'flat_flat') $cod_zones = split("[:,]", MODULE_ORDER_TOTAL_COD_FEE_FLAT);
          if ($_SESSION['shipping']['id'] == 'item_item') $cod_zones = split("[:,]", MODULE_ORDER_TOTAL_COD_FEE_ITEM);
          if ($_SESSION['shipping']['id'] == 'table_table') $cod_zones = split("[:,]", MODULE_ORDER_TOTAL_COD_FEE_TABLE);
          if ($_SESSION['shipping']['id'] == 'zones_zones') $cod_zones = split("[:,]", MODULE_ORDER_TOTAL_COD_FEE_ZONES);
          if ($_SESSION['shipping']['id'] == 'ap_ap') $cod_zones = split("[:,]", MODULE_ORDER_TOTAL_COD_FEE_AP);
          if ($_SESSION['shipping']['id'] == 'dp_dp') $cod_zones = split("[:,]", MODULE_ORDER_TOTAL_COD_FEE_DP);

            for ($i = 0; $i < count($cod_zones); $i++) {
            if ($cod_zones[$i] == $order->billing['country']['iso_code_2']) {
                  $cod_cost = $cod_zones[$i + 1];
                  $cod_country = true;
                  //print('match' . $cod_zones[$i] . ': ' . $cod_cost);
                  break;
                } elseif ($cod_zones[$i] == '00') {
                  $cod_cost = $cod_zones[$i + 1];
                  $cod_country = true;
                  //print('match' . $i . ': ' . $cod_cost);
                  break;
                } else {
                  //print('no match');
                }
              $i++;
            }
          } else {
            //COD selected, but no shipping module which offers COD
          }

        if ($cod_country) {

            $cod_tax = xtc_get_tax_rate(MODULE_ORDER_TOTAL_COD_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);
            $cod_tax_description = xtc_get_tax_description(MODULE_ORDER_TOTAL_COD_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);
        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1) {
            $order->info['tax'] += xarModAPIFunc('commerce','user','add_tax',array('price' =>$cod_cost,'tax' =>$cod_tax))-$cod_cost;
            $order->info['tax_groups'][TAX_ADD_TAX . "$cod_tax_description"] += xarModAPIFunc('commerce','user','add_tax',array('price' =>$cod_cost,'tax' =>$cod_tax))-$cod_cost;
            $order->info['total'] += $cod_cost + (xarModAPIFunc('commerce','user','add_tax',array('price' =>$cod_cost,'tax' =>$cod_tax))-$cod_cost);
            $cod_cost_value= xarModAPIFunc('commerce','user','add_tax',array('price' =>$cod_cost,'tax' =>$cod_tax));
            $cod_cost= xtc_format_price($cod_cost_value, $price_special=1, $calculate_currencies=true);
        }
        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
            $order->info['tax'] += xarModAPIFunc('commerce','user','add_tax',array('price' =>$cod_cost,'tax' =>$cod_tax))-$cod_cost;
            $order->info['tax_groups'][TAX_NO_TAX . "$cod_tax_description"] += xarModAPIFunc('commerce','user','add_tax',array('price' =>$cod_cost,'tax' =>$cod_tax))-$cod_cost;
            $cod_cost_value=$cod_cost;
            $cod_cost= xtc_format_price($cod_cost, $price_special=1, $calculate_currencies=true);
            $order->info['subtotal'] += $cod_cost_value;
            $order->info['total'] += $cod_cost_value;
        }
        if (!$cod_cost_value) {
           $cod_cost_value=$cod_cost;
           $cod_cost= xtc_format_price($cod_cost, $price_special=1, $calculate_currencies=true);
           $order->info['total'] += $cod_cost_value;
        }
            $this->output[] = array('title' => $this->title . ':',
                                    'text' => $cod_cost,
                                    'value' => $cod_cost_value);
        } else {
//Following code should be improved if we can't get the shipping modules disabled, who don't allow COD
// as well as countries who do not have cod
//          $this->output[] = array('title' => $this->title . ':',
//                                  'text' => 'No COD for this module.',
//                                  'value' => '');
        }
      }
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = new xenQuery("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_COD_STATUS'");
        $this->_check = $check_query->getrows();
      }
      return $this->_check;
    }

    function keys() {
      return array('MODULE_ORDER_TOTAL_COD_STATUS', 'MODULE_ORDER_TOTAL_COD_SORT_ORDER', 'MODULE_ORDER_TOTAL_COD_FEE_FLAT', 'MODULE_ORDER_TOTAL_COD_FEE_ITEM', 'MODULE_ORDER_TOTAL_COD_FEE_TABLE', 'MODULE_ORDER_TOTAL_COD_FEE_ZONES', 'MODULE_ORDER_TOTAL_COD_FEE_AP', 'MODULE_ORDER_TOTAL_COD_FEE_DP', 'MODULE_ORDER_TOTAL_COD_TAX_CLASS');
    }

    function install() {
      new xenQuery("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_COD_STATUS', 'true', '6', '0', 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");

      new xenQuery("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_COD_SORT_ORDER', '5', '6', '0', now())");

      new xenQuery("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_COD_FEE_FLAT', 'AT:3.00,DE:3.58,00:9.99', '6', '0', now())");

      new xenQuery("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_COD_FEE_ITEM', 'AT:3.00,DE:3.58,00:9.99', '6', '0', now())");

      new xenQuery("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_COD_FEE_TABLE', 'AT:3.00,DE:3.58,00:9.99', '6', '0', now())");

      new xenQuery("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_COD_FEE_ZONES', 'CA:4.50,US:3.00,00:9.99', '6', '0', now())");

      new xenQuery("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_COD_FEE_AP', 'AT:3.63,00:9.99', '6', '0', now())");

      new xenQuery("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_COD_FEE_DP', 'DE:4.00,00:9.99', '6', '0', now())");

      new xenQuery("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_ORDER_TOTAL_COD_TAX_CLASS', '0', '6', '0', 'xtc_get_tax_class_title', 'xtc_cfg_pull_down_tax_classes(', now())");
    }

    function remove() {
      new xenQuery("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }
  }
?>

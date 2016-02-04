<?php 
/**
 * Payments Module
 *
 * @package modules
 * @subpackage payments
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2016 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
  
  sys::import('modules.payments.class.basicpayment');
  
  class WorldPay extends BasicPayment
  {

    public function __construct() 
    {
        global $order;
        $this->code = 'worldpay';
        $this->title = MODULE_PAYMENT_WORLDPAY_TEXT_TITLE;
        $this->description = MODULE_PAYMENT_WORLDPAY_TEXT_DESC;
        $this->sort_order = MODULE_PAYMENT_WORLDPAY_SORT_ORDER;
        $this->enabled = ((MODULE_PAYMENT_WORLDPAY_STATUS == 'True') ? true : false);
        $this->info = MODULE_PAYMENT_WORLDPAY_TEXT_INFO;
        if ((int) MODULE_PAYMENT_WORLDPAY_ORDER_STATUS_ID > 0) {
            $this->order_status = MODULE_PAYMENT_WORLDPAY_ORDER_STATUS_ID;
        }

        if (is_object($order))
            $this->update_status();

        $this->form_action_url = 'https://select.worldpay.com/wcc/purchase';

    }

    public function update_status(Array $args=array()) 
    {
        global $order;

        if (($this->enabled == true) && ((int) MODULE_PAYMENT_WORLDPAY_ZONE > 0)) {
            $check_flag = false;
            $check_query = xtc_db_query("select zone_id from ".TABLE_ZONES_TO_GEO_ZONES." where geo_zone_id = '".MODULE_PAYMENT_WORLDPAY_ZONE."' and zone_country_id = '".$order->billing['country']['id']."' order by zone_id");
            while ($check = xtc_db_fetch_array($check_query)) {
                if ($check['zone_id'] < 1) {
                    $check_flag = true;
                    break;
                }
                elseif ($check['zone_id'] == $order->billing['zone_id']) {
                    $check_flag = true;
                    break;
                }
            }
            if ($check_flag == false) {
                $this->enabled = false;
            }

        }
    }

    public function selection() 
    {
        return array ('id' => $this->code, 'module' => $this->title, 'description' => $this->info);
    }

    public function process_button() 
    {
        global $order, $xtPrice;

        $worldpay_url = xtc_session_name().'='.xtc_session_id();
        $total = number_format($xtPrice->xtcCalculateCurr($order->info['total']), $xtPrice->get_decimal_places($_SESSION['currency']), '.', '');

        $process_button_string = xtc_draw_hidden_field('instId', MODULE_PAYMENT_WORLDPAY_ID).xtc_draw_hidden_field('currency', $_SESSION['currency']).xtc_draw_hidden_field('desc', 'Purchase from '.STORE_NAME).xtc_draw_hidden_field('cartId', $worldpay_url).xtc_draw_hidden_field('amount', $total);

        // Pre Auth Mod 3/1/2002 - Graeme Conkie
        if (MODULE_PAYMENT_WORLDPAY_USEPREAUTH == 'True')
            $process_button_string .= xtc_draw_hidden_field('authMode', MODULE_PAYMENT_WORLDPAY_PREAUTH);

        // Ian-san: Create callback and language links here 6/4/2003:
        $language_code_raw = xtc_db_query("select code from ".TABLE_LANGUAGES." where languages_id ='".$_SESSION['languages_id']."'");
        $language_code_array = xtc_db_fetch_array($language_code_raw);
        $language_code = $language_code_array['code'];

        $address = htmlspecialchars($order->customer['street_address']."\n".$order->customer['suburb']."\n".$order->customer['city']."\n".$order->customer['state'], ENT_QUOTES);

        $process_button_string .= xtc_draw_hidden_field('testMode', MODULE_PAYMENT_WORLDPAY_MODE).xtc_draw_hidden_field('name', $order->customer['firstname'].' '.$order->customer['lastname']).xtc_draw_hidden_field('address', $address).xtc_draw_hidden_field('postcode', $order->customer['postcode']).xtc_draw_hidden_field('country', $order->customer['country']['iso_code_2']).xtc_draw_hidden_field('tel', $order->customer['telephone']).xtc_draw_hidden_field('myvar', 'Y').xtc_draw_hidden_field('fax', $order->customer['fax']).xtc_draw_hidden_field('email', $order->customer['email_address']).

        // Ian-san: Added dynamic callback and languages link here 6/4/2003:
        xtc_draw_hidden_field('lang', $language_code).xtc_draw_hidden_field('MC_callback', xtc_href_link(wpcallback).'.php').xtc_draw_hidden_field('MC_XTCsid', $XTCsid);

        // Ian-san: Added MD5 here 6/4/2003:
        if (MODULE_PAYMENT_WORLDPAY_USEMD5 == '1') {
            $md5_signature_fields = 'amount:language:email';
            $md5_signature = MODULE_PAYMENT_WORLDPAY_MD5KEY.':'. (number_format($order->info['total'] * $currencies->get_value($currency), $currencies->get_decimal_places($currency), '.', '')).':'.$language_code.':'.$order->customer['email_address'];
            $md5_signature_md5 = md5($md5_signature);

            $process_button_string .= xtc_draw_hidden_field('signatureFields', $md5_signature_fields).xtc_draw_hidden_field('signature', $md5_signature_md5);
        }
        return $process_button_string;
    }

    public function after_process() 
    {
    global $insert_id;
    if ($this->order_status) xtc_db_query("UPDATE ". TABLE_ORDERS ." SET orders_status='".$this->order_status."' WHERE orders_id='".$insert_id."'");
    }

    public function check() 
    {
        if (!isset ($this->_check)) 
        {
            $check_query = xtc_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_WORLDPAY_STATUS'");
            $this->_check = xtc_db_num_rows($check_query);
        }
        return $this->_check;
    }

    public function install() 
    {
        xtc_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_WORLDPAY_STATUS', 'True', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        xtc_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_WORLDPAY_ID', '00000', '6', '2', now())");
        xtc_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_WORLDPAY_MODE', '100', '6', '5', now())");
        xtc_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_WORLDPAY_ALLOWED', '', '6', '0', now())");
        // Ian-san: Added MD5 here 6/4/2003:
        xtc_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_WORLDPAY_USEMD5', '0', '6', '4', now())");
        xtc_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_WORLDPAY_MD5KEY', '', '6', '5', now())");

        // Pre Auth Mod - Graeme Conkie 13/1/2003
        xtc_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_WORLDPAY_SORT_ORDER', '0', '6', '0', now())");
        xtc_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_WORLDPAY_USEPREAUTH', 'False', '6', '3', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
        xtc_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_WORLDPAY_ORDER_STATUS_ID', '0', '6', '0', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
        xtc_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_WORLDPAY_PREAUTH', 'A', '6', '4', now())");
        // Paulz zone control 04/04/2004        
        xtc_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_WORLDPAY_ZONE', '0', '6', '2', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
        // Ian-san: Added MD5 here 6/4/2003:
        xtc_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_WORLDPAY_USEMD5'");
        xtc_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_WORLDPAY_MD5KEY'");
    }

    public function remove() 
    {
        xtc_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in ('".implode("', '", $this->keys())."')");
    }

    public function keys() 
    {
        return array ('MODULE_PAYMENT_WORLDPAY_STATUS', 'MODULE_PAYMENT_WORLDPAY_ID', 'MODULE_PAYMENT_WORLDPAY_MODE', 'MODULE_PAYMENT_WORLDPAY_ALLOWED', 'MODULE_PAYMENT_WORLDPAY_USEPREAUTH', 'MODULE_PAYMENT_WORLDPAY_PREAUTH', 'MODULE_PAYMENT_WORLDPAY_ZONE', 'MODULE_PAYMENT_WORLDPAY_SORT_ORDER', 'MODULE_PAYMENT_WORLDPAY_ORDER_STATUS_ID');
    }
}
?>
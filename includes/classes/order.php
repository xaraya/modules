<?php
/* -----------------------------------------------------------------------------------------
   $Id: order.php,v 1.4 2003/12/13 17:16:12 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(order.php,v 1.32 2003/02/26); www.oscommerce.com
   (c) 2003  nextcommerce (order.php,v 1.28 2003/08/18); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  // include needed functions
  require_once(DIR_FS_INC . 'xtc_get_countries.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_tax_description.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_single_products_price.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_products_attribute_price_checkout.inc.php');

  class order {
    var $info, $totals, $products, $customer, $delivery, $content_type;

    function order($order_id = '') {
      $this->info = array();
      $this->totals = array();
      $this->products = array();
      $this->customer = array();
      $this->delivery = array();

      if (xarModAPIFunc('commerce','user','not_null',array('arg' => $order_id))) {
        $this->query($order_id);
      } else {
        $this->cart();
      }
    }

    function query($order_id) {

      $order_id = xtc_db_prepare_input($order_id);

      $order_query = new xenQuery("select customers_id, customers_name, customers_company, customers_street_address, customers_suburb, customers_city, customers_postcode, customers_state, customers_country, customers_telephone, customers_email_address, customers_address_format_id, delivery_name, delivery_company, delivery_street_address, delivery_suburb, delivery_city, delivery_postcode, delivery_state, delivery_country, delivery_address_format_id, billing_name, billing_company, billing_street_address, billing_suburb, billing_city, billing_postcode, billing_state, billing_country, billing_address_format_id, payment_method, cc_type, cc_owner, cc_number, cc_expires, currency, currency_value, date_purchased, orders_status, last_modified from " . TABLE_ORDERS . " where orders_id = '" . xtc_db_input($order_id) . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
      $order = $q->output();

      $totals_query = new xenQuery("select title, text,value from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . xtc_db_input($order_id) . "' order by sort_order");
      $q = new xenQuery();
      if(!$q->run()) return;
      while ($totals = $q->output()) {
        $this->totals[] = array('title' => $totals['title'],
                                'text' => xtc_format_price($totals['value'],$price_special=1,$calculate_currencies=false));
      }

      $order_total_query = new xenQuery("select text from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . $order_id . "' and class = 'ot_total'");
      $q = new xenQuery();
      if(!$q->run()) return;
      $order_total = $q->output();

      $shipping_method_query = new xenQuery("select title from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . $order_id . "' and class = 'ot_shipping'");
      $q = new xenQuery();
      if(!$q->run()) return;
      $shipping_method = $q->output();

      $order_status_query = new xenQuery("select orders_status_name from " . TABLE_ORDERS_STATUS . " where orders_status_id = '" . $order['orders_status'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
      $order_status = $q->output();

      $this->info = array('currency' => $order['currency'],
                          'currency_value' => $order['currency_value'],
                          'payment_method' => $order['payment_method'],
                          'cc_type' => $order['cc_type'],
                          'cc_owner' => $order['cc_owner'],
                          'cc_number' => $order['cc_number'],
                          'cc_expires' => $order['cc_expires'],
                          'date_purchased' => $order['date_purchased'],
                          'orders_status' => $order_status['orders_status_name'],
                          'last_modified' => $order['last_modified'],
                          'total' => strip_tags($order_total['text']),
                          'shipping_method' => ((substr($shipping_method['title'], -1) == ':') ? substr(strip_tags($shipping_method['title']), 0, -1) : strip_tags($shipping_method['title'])),
                          'comments' => $_SESSION['comments']
                          );

      $this->customer = array('id' => $order['customers_id'],
                              'name' => $order['customers_name'],
                              'company' => $order['customers_company'],
                              'street_address' => $order['customers_street_address'],
                              'suburb' => $order['customers_suburb'],
                              'city' => $order['customers_city'],
                              'postcode' => $order['customers_postcode'],
                              'state' => $order['customers_state'],
                              'country' => $order['customers_country'],
                              'format_id' => $order['customers_address_format_id'],
                              'telephone' => $order['customers_telephone'],
                              'email_address' => $order['customers_email_address']);

      $this->delivery = array('name' => $order['delivery_name'],
                              'company' => $order['delivery_company'],
                              'street_address' => $order['delivery_street_address'],
                              'suburb' => $order['delivery_suburb'],
                              'city' => $order['delivery_city'],
                              'postcode' => $order['delivery_postcode'],
                              'state' => $order['delivery_state'],
                              'country' => $order['delivery_country'],
                              'format_id' => $order['delivery_address_format_id']);

      if (empty($this->delivery['name']) && empty($this->delivery['street_address'])) {
        $this->delivery = false;
      }

      $this->billing = array('name' => $order['billing_name'],
                             'company' => $order['billing_company'],
                             'street_address' => $order['billing_street_address'],
                             'suburb' => $order['billing_suburb'],
                             'city' => $order['billing_city'],
                             'postcode' => $order['billing_postcode'],
                             'state' => $order['billing_state'],
                             'country' => $order['billing_country'],
                             'format_id' => $order['billing_address_format_id']);

      $index = 0;
      $orders_products_query = new xenQuery("select orders_products_id, products_id, products_name, products_model, products_price, products_tax, products_quantity, final_price from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . xtc_db_input($order_id) . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
      while ($orders_products = $q->output()) {
        $this->products[$index] = array('qty' => $orders_products['products_quantity'],
                                        'id' => $orders_products['products_id'],
                                        'name' => $orders_products['products_name'],
                                        'model' => $orders_products['products_model'],
                                        'tax' => $orders_products['products_tax'],
                                        //'price' => $orders_products['products_price'],
                    'price'=>xarModAPIFunc('commerce','user','get_products_price',array('products_id' =>$orders_products['products_id'],'price_special' =>$price_special=0,'quantity' =>$quantity=1)),
                                        'final_price' => $orders_products['final_price']);

        $subindex = 0;
        $attributes_query = new xenQuery("select products_options, products_options_values, options_values_price, price_prefix from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_id = '" . xtc_db_input($order_id) . "' and orders_products_id = '" . $orders_products['orders_products_id'] . "'");
        if ($attributes_query->getrows()) {
      $q = new xenQuery();
      if(!$q->run()) return;
          while ($attributes = $q->output()) {
            $this->products[$index]['attributes'][$subindex] = array('option' => $attributes['products_options'],
                                                                     'value' => $attributes['products_options_values'],
                                                                     'prefix' => $attributes['price_prefix'],
                                                                     'price' => $attributes['options_values_price']);

            $subindex++;
          }
        }

        $this->info['tax_groups']["{$this->products[$index]['tax']}"] = '1';

        $index++;
      }
    }

    function cart() {
      global $currencies;

      $this->content_type = $_SESSION['cart']->get_content_type();

      $customer_address_query = new xenQuery("select c.customers_firstname, c.customers_gender,c.customers_lastname, c.customers_telephone, c.customers_email_address, ab.entry_company, ab.entry_street_address, ab.entry_suburb, ab.entry_postcode, ab.entry_city, ab.entry_zone_id, z.zone_name, co.countries_id, co.countries_name, co.countries_iso_code_2, co.countries_iso_code_3, co.address_format_id, ab.entry_state from " . TABLE_CUSTOMERS . " c, " . TABLE_ADDRESS_BOOK . " ab left join " . TABLE_ZONES . " z on (ab.entry_zone_id = z.zone_id) left join " . TABLE_COUNTRIES . " co on (ab.entry_country_id = co.countries_id) where c.customers_id = '" . $_SESSION['customer_id'] . "' and ab.customers_id = '" . $_SESSION['customer_id'] . "' and c.customers_default_address_id = ab.address_book_id");
      $q = new xenQuery();
      if(!$q->run()) return;
      $customer_address = $q->output();

      $shipping_address_query = new xenQuery("select ab.entry_firstname, ab.entry_lastname, ab.entry_company, ab.entry_street_address, ab.entry_suburb, ab.entry_postcode, ab.entry_city, ab.entry_zone_id, z.zone_name, ab.entry_country_id, c.countries_id, c.countries_name, c.countries_iso_code_2, c.countries_iso_code_3, c.address_format_id, ab.entry_state from " . TABLE_ADDRESS_BOOK . " ab left join " . TABLE_ZONES . " z on (ab.entry_zone_id = z.zone_id) left join " . TABLE_COUNTRIES . " c on (ab.entry_country_id = c.countries_id) where ab.customers_id = '" . $_SESSION['customer_id'] . "' and ab.address_book_id = '" . $_SESSION['sendto'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
      $shipping_address = $q->output();

      $billing_address_query = new xenQuery("select ab.entry_firstname, ab.entry_lastname, ab.entry_company, ab.entry_street_address, ab.entry_suburb, ab.entry_postcode, ab.entry_city, ab.entry_zone_id, z.zone_name, ab.entry_country_id, c.countries_id, c.countries_name, c.countries_iso_code_2, c.countries_iso_code_3, c.address_format_id, ab.entry_state from " . TABLE_ADDRESS_BOOK . " ab left join " . TABLE_ZONES . " z on (ab.entry_zone_id = z.zone_id) left join " . TABLE_COUNTRIES . " c on (ab.entry_country_id = c.countries_id) where ab.customers_id = '" . $_SESSION['customer_id'] . "' and ab.address_book_id = '" . $_SESSION['billto'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
      $billing_address = $q->output();

      $tax_address_query = new xenQuery("select ab.entry_country_id, ab.entry_zone_id from " . TABLE_ADDRESS_BOOK . " ab left join " . TABLE_ZONES . " z on (ab.entry_zone_id = z.zone_id) where ab.customers_id = '" . $_SESSION['customer_id'] . "' and ab.address_book_id = '" . ($this->content_type == 'virtual' ? $_SESSION['billto'] : $_SESSION['sendto']) . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
      $tax_address = $q->output();

      $this->info = array('order_status' => DEFAULT_ORDERS_STATUS_ID,
                          'currency' => $_SESSION['currency'],
                          'currency_value' => $currencies->currencies[$_SESSION['currency']]['value'],
                          'payment_method' => $_SESSION['payment'],
                          'cc_type' => $GLOBALS['cc_type'],
                          'cc_owner' => $GLOBALS['cc_owner'],
                          'cc_number' => $GLOBALS['cc_number'],
                          'cc_expires' => $GLOBALS['cc_expires'],
                          'shipping_method' => $_SESSION['shipping']['title'],
                          'shipping_cost' => $_SESSION['shipping']['cost'],
                          'comments' => $_SESSION['comments'],
                           // modifications for CAO-Faktura export
                          'shipping_class'=>$_SESSION['shipping']['id'],
                          'payment_class' => $_SESSION['payment'],
                          // modifications end
                          );

      if (isset($_SESSION['payment']) && is_object($_SESSION['payment'])) {
        $this->info['payment_method'] = $_SESSION['payment']->title;
        // modifications for CAO-Faktura export
        $this->info['payment_class'] = $_SESSION['payment']->title;
        // modifications end
        if ( isset($_SESSION['payment']->order_status) && is_numeric($_SESSION['payment']->order_status) && ($_SESSION['payment']->order_status > 0) ) {
          $this->info['order_status'] = $_SESSION['payment']->order_status;
        }
      }

      $this->customer = array('firstname' => $customer_address['customers_firstname'],
                              'lastname' => $customer_address['customers_lastname'],
                              'gender' => $customer_address['customers_gender'],
                              'company' => $customer_address['entry_company'],
                              'street_address' => $customer_address['entry_street_address'],
                              'suburb' => $customer_address['entry_suburb'],
                              'city' => $customer_address['entry_city'],
                              'postcode' => $customer_address['entry_postcode'],
                              'state' => ((xarModAPIFunc('commerce','user','not_null',array('arg' => $customer_address['entry_state']))) ? $customer_address['entry_state'] : $customer_address['zone_name']),
                              'zone_id' => $customer_address['entry_zone_id'],
                              'country' => array('id' => $customer_address['countries_id'], 'title' => $customer_address['countries_name'], 'iso_code_2' => $customer_address['countries_iso_code_2'], 'iso_code_3' => $customer_address['countries_iso_code_3']),
                              'format_id' => $customer_address['address_format_id'],
                              'telephone' => $customer_address['customers_telephone'],
                              'email_address' => $customer_address['customers_email_address']);

      $this->delivery = array('firstname' => $shipping_address['entry_firstname'],
                              'lastname' => $shipping_address['entry_lastname'],
                              'company' => $shipping_address['entry_company'],
                              'street_address' => $shipping_address['entry_street_address'],
                              'suburb' => $shipping_address['entry_suburb'],
                              'city' => $shipping_address['entry_city'],
                              'postcode' => $shipping_address['entry_postcode'],
                              'state' => ((xarModAPIFunc('commerce','user','not_null',array('arg' => $shipping_address['entry_state']))) ? $shipping_address['entry_state'] : $shipping_address['zone_name']),
                              'zone_id' => $shipping_address['entry_zone_id'],
                              'country' => array('id' => $shipping_address['countries_id'], 'title' => $shipping_address['countries_name'], 'iso_code_2' => $shipping_address['countries_iso_code_2'], 'iso_code_3' => $shipping_address['countries_iso_code_3']),
                              'country_id' => $shipping_address['entry_country_id'],
                              'format_id' => $shipping_address['address_format_id']);

      $this->billing = array('firstname' => $billing_address['entry_firstname'],
                             'lastname' => $billing_address['entry_lastname'],
                             'company' => $billing_address['entry_company'],
                             'street_address' => $billing_address['entry_street_address'],
                             'suburb' => $billing_address['entry_suburb'],
                             'city' => $billing_address['entry_city'],
                             'postcode' => $billing_address['entry_postcode'],
                             'state' => ((xarModAPIFunc('commerce','user','not_null',array('arg' => $billing_address['entry_state']))) ? $billing_address['entry_state'] : $billing_address['zone_name']),
                             'zone_id' => $billing_address['entry_zone_id'],
                             'country' => array('id' => $billing_address['countries_id'], 'title' => $billing_address['countries_name'], 'iso_code_2' => $billing_address['countries_iso_code_2'], 'iso_code_3' => $billing_address['countries_iso_code_3']),
                             'country_id' => $billing_address['entry_country_id'],
                             'format_id' => $billing_address['address_format_id']);

      $index = 0;
      $products = $_SESSION['cart']->get_products();
      for ($i=0, $n=sizeof($products); $i<$n; $i++) {
        $this->products[$index] = array('qty' => $products[$i]['quantity'],
                                        'name' => $products[$i]['name'],
                                        'model' => $products[$i]['model'],
                                        'tax' => xtc_get_tax_rate($products[$i]['tax_class_id'], $tax_address['entry_country_id'], $tax_address['entry_zone_id']),
                                        'tax_description' => xtc_get_tax_description($products[$i]['tax_class_id'], $tax_address['entry_country_id'], $tax_address['entry_zone_id']),
                                        'price' =>  (xarModAPIFunc('commerce','user','get_products_price',array('products_id' =>$products[$i]['id'],'price_special' =>$price_special=0,'quantity' =>$quantity=$products[$i]['quantity']))+ xtc_get_products_attribute_price_checkout($_SESSION['cart']->attributes_price($products[$i]['id']),0,0,1,'')*$products[$i]['quantity'])/$products[$i]['quantity'],
                                    'final_price' => xarModAPIFunc('commerce','user','get_products_price',array('products_id' =>$products[$i]['id'],'price_special' =>$price_special=0,'quantity' =>$quantity=$products[$i]['quantity'])) + xtc_get_products_attribute_price_checkout($_SESSION['cart']->attributes_price($products[$i]['id']),0,0,1,'')*$products[$i]['quantity'],
                    'weight' => $products[$i]['weight'],
                                        'id' => $products[$i]['id']);

        if ($products[$i]['attributes']) {
          $subindex = 0;
          reset($products[$i]['attributes']);
          while (list($option, $value) = each($products[$i]['attributes'])) {
            $attributes_query = new xenQuery("select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa where pa.products_id = '" . $products[$i]['id'] . "' and pa.options_id = '" . $option . "' and pa.options_id = popt.products_options_id and pa.options_values_id = '" . $value . "' and pa.options_values_id = poval.products_options_values_id and popt.language_id = '" . $_SESSION['languages_id'] . "' and poval.language_id = '" . $_SESSION['languages_id'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
            $attributes = $q->output();

            $this->products[$index]['attributes'][$subindex] = array('option' => $attributes['products_options_name'],
                                                                     'value' => $attributes['products_options_values_name'],
                                                                     'option_id' => $option,
                                                                     'value_id' => $value,
                                                                     'prefix' => $attributes['price_prefix'],
                                                                     'price' => $attributes['options_values_price']);

            $subindex++;
          }
        }

        $shown_price = $this->products[$index]['final_price'];
        $this->info['subtotal'] += $shown_price;
        if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1){
          $shown_price_tax = $shown_price-($shown_price/100 * $_SESSION['customers_status']['customers_status_ot_discount']);
        }

        $products_tax = $this->products[$index]['tax'];
        $products_tax_description = $this->products[$index]['tax_description'];
        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == '1') {
          if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1) {
            $this->info['tax'] += $shown_price_tax - ($shown_price_tax / (($products_tax < 10) ? "1.0" . str_replace('.', '', $products_tax) : "1." . str_replace('.', '', $products_tax)));
            $this->info['tax_groups'][TAX_ADD_TAX."$products_tax_description"] += (($shown_price_tax /(100+$products_tax)) * $products_tax);
          } else {
            $this->info['tax'] += $shown_price - ($shown_price / (($products_tax < 10) ? "1.0" . str_replace('.', '', $products_tax) : "1." . str_replace('.', '', $products_tax)));
            $this->info['tax_groups'][TAX_ADD_TAX . "$products_tax_description"] += (($shown_price /(100+$products_tax)) * $products_tax);
          }
        } else {
          if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1) {
            $this->info['tax'] += ($shown_price_tax/100) * ($products_tax);
            $this->info['tax_groups'][TAX_NO_TAX . "$products_tax_description"] += ($shown_price_tax/100) * ($products_tax);
          } else {
            $this->info['tax'] += ($shown_price/100) * ($products_tax);
            $this->info['tax_groups'][TAX_NO_TAX . "$products_tax_description"] += ($shown_price/100) * ($products_tax);
          }
        }

        $index++;
      }

      if ($_SESSION['customers_status']['customers_status_show_price_tax'] == '0') {
        $this->info['total'] = $this->info['subtotal']  + $this->info['shipping_cost'];
        if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == '1') {
          $this->info['total'] -= ($this->info['subtotal'] /100 * $_SESSION['customers_status']['customers_status_ot_discount']);
        }
      } else {
        $this->info['total'] = $this->info['subtotal']  + $this->info['shipping_cost'];
        if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == '1') {
          $this->info['total'] -= ($this->info['subtotal'] /100 * $_SESSION['customers_status']['customers_status_ot_discount']);
        }
      }
    }
  }
?>
<?php
/* -----------------------------------------------------------------------------------------
   $Id: print_packingslip.php,v 1.3 2003/11/10 15:51:49 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2003  nextcommerce (print_order.php,v 1.1 2003/08/19); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  require('includes/application_top.php');
  // include needed functions
  require_once(DIR_FS_INC .'xtc_get_order_data.inc.php');
  require_once(DIR_FS_INC .'xtc_get_attributes_model.inc.php');

//  $smarty = new Smarty;

  $order_query_check = new xenQuery("SELECT
                    customers_id
                    FROM ".TABLE_ORDERS."
                    WHERE orders_id='".$_GET['oID']."'");

      $q = new xenQuery();
      $q->run();
  $order_check = $q->output();
 // if ($_SESSION['customer_id'] == $order_check['customers_id'])
  //    {
    // get order data

    include(DIR_WS_CLASSES . 'order.php');
    $order = new order($_GET['oID']);
    $data['address_label_customer'] = xarModAPIFunc('commerce','user','address_format',array(
    'address_format_id' =>$order->customer['format_id'],
    'address' =>$order->customer,
    'html' =>1,
    'boln' =>'',
    'eoln' =>'<br>'));
    $data['address_label_shipping'] = xarModAPIFunc('commerce','user','address_format',array(
    'address_format_id' =>$order->delivery['format_id'],
    'address' =>$order->delivery,
    'html' =>1,
    'boln' =>'',
    'eoln' =>'<br>'));
    $data['address_label_payment'] = xarModAPIFunc('commerce','user','address_format',array(
    'address_format_id' =>$order->billing['format_id'],
    'address' =>$order->billing,
    'html' =>1,
    'boln' =>'',
    'eoln' =>'<br>'));

    // get products data
        $order_query=new xenQuery("SELECT
                        products_id,
                        orders_products_id,
                        products_model,
                        products_name,
                        final_price,
                        products_quantity
                        FROM ".TABLE_ORDERS_PRODUCTS."
                        WHERE orders_id='".$_GET['oID']."'");
        $order_data=array();
      $q = new xenQuery();
      $q->run();
        while ($order_data_values = $q->output()) {
            $attributes_query=new xenQuery("SELECT
                        products_options,
                        products_options_values,
                        price_prefix,
                        options_values_price
                        FROM ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES."
                        WHERE orders_products_id='".$order_data_values['orders_products_id']."'");
            $attributes_data='';
            $attributes_model='';
      $q = new xenQuery();
      $q->run();
            while ($attributes_data_values = $q->output()) {
            $attributes_data .='<br>'.$attributes_data_values['products_options'].':'.$attributes_data_values['products_options_values'];
            $attributes_model .='<br>'.xtc_get_attributes_model($order_data_values['products_id'],$attributes_data_values['products_options_values']);
            }
        $order_data[]=array(
                'PRODUCTS_MODEL' => $order_data_values['products_model'],
                'PRODUCTS_NAME' => $order_data_values['products_name'],
                'PRODUCTS_ATTRIBUTES' => $attributes_data,
                'PRODUCTS_ATTRIBUTES_MODEL' => $attributes_model,
                'PRODUCTS_PRICE' => xtc_format_price($order_data_values['final_price'],$price_special=1,$calculate_currencies=0,$show_currencies=1),
                'PRODUCTS_QTY' => $order_data_values['products_quantity']);
        }
    // get order_total data
    $oder_total_query=new xenQuery("SELECT
                    title,
                    text,
                    sort_order
                    FROM ".TABLE_ORDERS_TOTAL."
                    WHERE orders_id='".$_GET['oID']."'
                    ORDER BY sort_order ASC");

    $order_total=array();
      $q = new xenQuery();
      $q->run();
    while ($oder_total_values = $q->output()) {

    $order_total[]=array(
            'TITLE' => $oder_total_values['title'],
            'TEXT' => $oder_total_values['text']);
    }

    // assign language to template for caching
    $data['language'] = $_SESSION['language'];
    $data['oID'] = $_GET['oID'];

    include(DIR_FS_CATALOG.'lang/'.$_SESSION['language'].'/modules/payment/'.$order->info['payment_method'].'.php');
    $payment_method=constant(strtoupper('MODULE_PAYMENT_'.$order->info['payment_method'].'_TEXT_TITLE'));
    $data['PAYMENT_METHOD'] = $payment_method;
    $data['DATE'] = $order->info['date_purchased'];
    $data['order_data'] = $order_data;
    $data['order_total'] = $order_total;

    // dont allow cache
    $smarty->caching = false;

    $smarty->template_dir=DIR_FS_CATALOG.'templates';
    $smarty->compile_dir=DIR_FS_CATALOG.'templates_c';
    $smarty->config_dir=DIR_FS_CATALOG.'lang';

    $smarty->display(CURRENT_TEMPLATE . '/admin/print_packingslip.html');
//  } else {

//      $smarty->display(CURRENT_TEMPLATE . '/error_message.html');
//  }

?>
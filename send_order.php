<?php
/* -----------------------------------------------------------------------------------------
   $Id: send_order.php,v 1.3 2003/12/30 09:01:13 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2003  nextcommerce (send_order.php,v 1.1 2003/08/24); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  require_once(DIR_FS_INC . 'xtc_get_order_data.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_attributes_model.inc.php');
  // check if customer is allowed to send this order!
  $order_query_check = new xenQuery("SELECT
                    customers_id
                    FROM ".TABLE_ORDERS."
                    WHERE orders_id='".$insert_id."'");

  $order_check = $q->output();
  if ($_SESSION['customer_id'] == $order_check['customers_id'])
    {

    $order = new order($insert_id);


    $data['address_label_customer'] = xarModAPIFunc('commerce','user','address_format',array(
    'address_format_id' =>$order->customer['format_id'],
    'address' =>$order->customer,
    'html' => 1,
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
                        WHERE orders_id='".$insert_id."'");
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
            $attributes_data .=$attributes_data_values['products_options'].':'.$attributes_data_values['products_options_values'].'<br>';
            $attributes_model .=xtc_get_attributes_model($order_data_values['products_id'],$attributes_data_values['products_options_values']).'<br>';
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
                    WHERE orders_id='".$insert_id."'
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
    $data['language'] =  $_SESSION['language'];
    $data['tpl_path'] = 'templates/'.CURRENT_TEMPLATE.'/';
    $data['oID'] = $insert_id;

    include(DIR_WS_LANGUAGES.$_SESSION['language'].'/modules/payment/'.$order->info['payment_method'].'.php';
    $payment_method=constant(strtoupper('MODULE_PAYMENT_'.$order->info['payment_method'].'_TEXT_TITLE'));
    $data['PAYMENT_METHOD'] = $payment_method;
    $data['DATE'] = $order->info['date_purchased'];
    $data['order_data'] =  $order_data;
    $data['order_total'] =  $order_total;
    $data['NAME'] = $order->customer['name'];

    // dont allow cache
    $smarty->caching = false;

  $html_mail=$smarty->fetch(CURRENT_TEMPLATE.'/mail/order_mail.html');
  $txt_mail=$smarty->fetch(CURRENT_TEMPLATE.'/mail/order_mail.txt');

  // create subject
  $order_subject=str_replace('{$nr}',$insert_id,EMAIL_BILLING_SUBJECT_ORDER);
  $order_subject=str_replace('{$date}',strftime(DATE_FORMAT_LONG),$order_subject);
  $order_subject=str_replace('{$lastname}',$order->customer['firstname'],$order_subject);
  $order_subject=str_replace('{$firstname}',$order->customer['firstname'],$order_subject);

  // send mail
  xtc_php_mail(EMAIL_BILLING_ADDRESS,EMAIL_BILLING_NAME,$order->customer['email_address'] ,$order->customer['firstname'] . ' ' . $order->customer['lastname'] , EMAIL_BILLING_FORWARDING_STRING, EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', $order_subject, $html_mail , $txt_mail );

} else {
$data['ERROR'] = 'You are not allowed to view this order!';
$smarty->display(CURRENT_TEMPLATE . '/error_message.html');
}


?>
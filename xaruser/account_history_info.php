<?php
// ----------------------------------------------------------------------
// Copyright (C) 2004: Marc Lutolf (marcinmilan@xaraya.com)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003 XT-Commerce
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------

  // create smarty elements
//  $smarty = new Smarty;
  // include boxes
  require(DIR_WS_INCLUDES.'boxes.php');
  // include needed functions
  require_once(DIR_FS_INC . 'xtc_get_all_get_params.inc.php');
  require_once(DIR_FS_INC . 'xtc_display_tax_value.inc.php');

  if (!isset($_SESSION['customer_id'])) {

    xarRedirectResponse(xarModURL('commerce','user','login', '', 'SSL'));
  }

  if (!isset($_GET['order_id']) || (isset($_GET['order_id']) && !is_numeric($_GET['order_id']))) {
    xarRedirectResponse(xarModURL('commerce','user',(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
  }

  $customer_info_query = new xenQuery("select customers_id from " . TABLE_ORDERS . " where orders_id = '". (int)$_GET['order_id'] . "'");
      $q = new xenQuery();
      $q->run();
  $customer_info = $q->output();
  if ($customer_info['customers_id'] != $_SESSION['customer_id']) {
    xarRedirectResponse(xarModURL('commerce','user',(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
  }


  $breadcrumb->add(NAVBAR_TITLE_1_ACCOUNT_HISTORY_INFO, xarModURL('commerce','user',(FILENAME_ACCOUNT, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2_ACCOUNT_HISTORY_INFO, xarModURL('commerce','user',(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
  $breadcrumb->add(sprintf(NAVBAR_TITLE_3_ACCOUNT_HISTORY_INFO, $_GET['order_id']), xarModURL('commerce','user',(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id=' . $_GET['order_id'], 'SSL'));

  require(DIR_WS_CLASSES . 'order.php');
  $order = new order($_GET['order_id']);
 require(DIR_WS_INCLUDES . 'header.php');

 $data['ORDER_NUMBER'] = $_GET['order_id'];
 $data['ORDER_DATE'] = xarModAPIFunc('commerce','user','date_long',array('raw_date' =>$order->info['date_purchased']));
 $data['ORDER_STATUS'] = $order->info['orders_status'];
 $data['BILLING_LABEL'] = xarModAPIFunc('commerce','user','address_format',array(
    'address_format_id' =>$order->billing['format_id'],
    'address' =>$order->billing,
    'html' =>1,
    'boln' =>' ',
    'eoln' =>'<br>'));
 $data['PRODUCTS_EDIT'] = xarModURL('commerce','user','shopping_cart', '', 'SSL');
 $data['SHIPPING_ADDRESS_EDIT'] = xarModURL('commerce','user',(FILENAME_CHECKOUT_SHIPPING_ADDRESS, '', 'SSL');
 $data['BILLING_ADDRESS_EDIT'] = xarModURL('commerce','user',(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL');
  $data['BUTTON_PRINT'] = '<img src="' . $language .'/buttons/button_print.gif" style="cursor:hand" onClick="window.open(\''. xarModURL('commerce','user',(FILENAME_PRINT_ORDER,'oID='.$_GET['order_id']).'\', \'popup\', \'toolbar=0, width=640, height=600\')">';


  if ($order->delivery != false) {

 $data['DELIVERY_LABEL',xarModAPIFunc('commerce','user','get_country_name',array(
    'address_format_id' =>$order->delivery['format_id'],
    'address' =>$order->delivery,
    'html' =>1,
    'boln' =>' ',
    'eoln' =>'<br>'));

    if ($order->info['shipping_method']) {
    $data['SHIPPING_METHOD'] = $order->info['shipping_method'];

    }

  }

  if (sizeof($order->info['tax_groups']) > 1) {

  } else {

  }

$data_products = '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
  for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
    $data_products .= '          <tr>' . "\n" .
         '            <td class="main" nowrap align="left" valign="top" width="">' . $order->products[$i]['qty'] .' x '.$order->products[$i]['name']. '</td>' . "\n" .
     '                <td class="main" align="right" valign="top">' .xarModAPIFunc('commerce','user','get_products_price',array('products_id' =>$order->products[$i]['id'],'price_special' =>$price_special=1,'price_special' =>$quantity=$order->products[$i]['qty'])). '</td></tr>' . "\n" ;



    if ( (isset($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0) ) {
      for ($j=0, $n2=sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
        $data_products .= '<tr>
        <td class="main" align="left" valign="top">
        <nobr><small>&nbsp;<i> - '
        . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'] .'
        </i></small></td>
        <td class="main" align="right" valign="top"><i><small>'
        .xtc_get_products_attribute_price_checkout($order->products[$i]['attributes'][$j]['price'],$order->products[$i]['tax'],1,$order->products[$i]['qty'],$order->products[$i]['attributes'][$j]['prefix']).
        '</i></small></nobr></td></tr>';
      }
    }

    $data_products .= '' . "\n";

    if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
      if (sizeof($order->info['tax_groups']) > 1) $data_products .= '            <td class="main" valign="top" align="right">' . xtc_display_tax_value($order->products[$i]['tax']) . '%</td>' . "\n";
    }
     $data_products .=    '          </tr>' . "\n";
  }
  $data_products .= '</table>';
      $data['PRODUCTS_BLOCK'] = $data_products;

       include(DIR_WS_LANGUAGES . '/' . $_SESSION['language'] . '/modules/payment/' . $order->info['payment_method'] . '.php');
          $data['PAYMENT_METHOD'] = constant(MODULE_PAYMENT_ . strtoupper($order->info['payment_method']) . _TEXT_TITLE);

$total_block='<table>';
  for ($i=0, $n=sizeof($order->totals); $i<$n; $i++) {
    $total_block.= '            <tr>' . "\n" .
         '                <td class="main" nowrap align="right" width="100%">' . $order->totals[$i]['title'] . '</td>' . "\n" .
         '                <td class="main" nowrap align="right">' . $order->totals[$i]['text'] . '</td>' . "\n" .
         '              </tr>' . "\n";
  }
  $total_block.='</table>';
    $data['TOTAL_BLOCK'] = $total_block;
$history_block='<table>';
  $statuses_query = new xenQuery("select os.orders_status_name, osh.date_added, osh.comments from " . TABLE_ORDERS_STATUS . " os, " . TABLE_ORDERS_STATUS_HISTORY . " osh where osh.orders_id = '" . (int)$_GET['order_id'] . "' and osh.orders_status_id = os.orders_status_id and os.language_id = '" . (int)$_SESSION['languages_id'] . "' order by osh.date_added");
      $q = new xenQuery();
      $q->run();
  while ($statuses = $q->output()) {
    $history_block.= '              <tr>' . "\n" .
         '                <td class="main" valign="top" width="70">' . xarModAPIFunc('commerce','user','date_short',array('raw_date' =>$statuses['date_added']))
 . '</td>' . "\n" .
         '                <td class="main" valign="top" width="70">' . $statuses['orders_status_name'] . '</td>' . "\n" .
         '                <td class="main" valign="top">' . (empty($statuses['comments']) ? '&nbsp;' : nl2br(htmlspecialchars($statuses['comments']))) . '</td>' . "\n" .
         '              </tr>' . "\n";
  }
  $history_block.='</table>';
  $data['HISTORY_BLOCK'] = $history_block;

 // if (DOWNLOAD_ENABLED == 'true') include(DIR_WS_MODULES . 'downloads.php');
$data['BUTTON_BACK'] = '<a href="' . xarModURL('commerce','user',(FILENAME_ACCOUNT_HISTORY, xtc_get_all_get_params(array('order_id')), 'SSL') . '">' .
xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_back.gif'),'alt' => IMAGE_BUTTON_BACK);
. '</a>';

  $data['language'] =  $_SESSION['language'];
  $data['PAYMENT_BLOCK'] = $payment_block;
  $smarty->caching = 0;
  return data;
?>
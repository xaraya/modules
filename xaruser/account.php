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
  require_once(DIR_FS_INC . 'xtc_count_customer_orders.inc.php');

  if (!xtc_session_is_registered('customer_id')) {

    xarRedirectResponse(xarModURL('commerce','user','login', '', 'SSL'));
  }

  $breadcrumb->add(NAVBAR_TITLE_ACCOUNT, xarModURL('commerce','user',(FILENAME_ACCOUNT, '', 'SSL'));

 require(DIR_WS_INCLUDES . 'header.php');


  if ($messageStack->size('account') > 0) {

$data['error_message'] = $messageStack->output('account');

  }
$order_content='';
  if (xtc_count_customer_orders() > 0) {

    $orders_query = new xenQuery("select o.orders_id, o.date_purchased, o.delivery_name, o.delivery_country, o.billing_name, o.billing_country, ot.text as order_total, s.orders_status_name from " . TABLE_ORDERS . " o, " . TABLE_ORDERS_TOTAL . " ot, " . TABLE_ORDERS_STATUS . " s where o.customers_id = '" . (int)$_SESSION['customer_id'] . "' and o.orders_id = ot.orders_id and ot.class = 'ot_total' and o.orders_status = s.orders_status_id and s.language_id = '" . (int)$_SESSION['languages_id'] . "' order by orders_id desc limit 3");
      $q = new xenQuery();
      $q->run();
    while ($orders = $q->output()) {
      if (xarModAPIFunc('commerce','user','not_null',array('arg' => $orders['delivery_name']))) {
        $order_name = $orders['delivery_name'];
        $order_country = $orders['delivery_country'];
      } else {
        $order_name = $orders['billing_name'];
        $order_country = $orders['billing_country'];
      }
     $order_content[]=array(
                        'ORDER_ID' =>$orders['orders_id'],
                        'ORDER_DATE' =>xarModAPIFunc('commerce','user','date_short',array('raw_date' =>$orders['date_purchased'])),
                        'ORDER_STATUS' =>$orders['orders_status_name'],
                        'ORDER_TOTAL' =>$orders['order_total'],
                        'ORDER_LINK' => xarModURL('commerce','user',(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id=' . $orders['orders_id'], 'SSL') ,
                        'ORDER_BUTTON' => '<a href="'.xarModURL('commerce','user',(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id=' . $orders['orders_id'], 'SSL') . '">' .
  xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'small_view.gif'),'alt' => SMALL_IMAGE_BUTTON_VIEW);
                        . '</a>');
   }

  }
  $data['LINK_EDIT'] = xarModURL('commerce','user',(FILENAME_ACCOUNT_EDIT, '', 'SSL');
  $data['LINK_ADDRESS'] = xarModURL('commerce','user',(FILENAME_ADDRESS_BOOK, '', 'SSL');
  $data['LINK_PASSWORD'] = xarModURL('commerce','user',(FILENAME_ACCOUNT_PASSWORD, '', 'SSL');
  $data['LINK_ORDERS'] = xarModURL('commerce','user',(FILENAME_ACCOUNT_HISTORY, '', 'SSL');
  $data['LINK_NEWSLETTER'] = xarModURL('commerce','user',(FILENAME_ACCOUNT_NEWSLETTERS, '', 'SSL');
  $data['LINK_NOTIFICATIONS'] = xarModURL('commerce','user',(FILENAME_ACCOUNT_NOTIFICATIONS, '', 'SSL');
  $data['LINK_ALL'] = xarModURL('commerce','user',(FILENAME_ACCOUNT_HISTORY, '', 'SSL');
  $data['order_content'] = $order_content;
  $data['language'] = $_SESSION['language'];

  $smarty->caching = 0;
  return data;
?>
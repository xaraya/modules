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
  require_once(DIR_FS_INC . 'xtc_get_all_get_params.inc.php');

  if (!isset($_SESSION['customer_id'])) {

    xarRedirectResponse(xarModURL('commerce','user','login', '', 'SSL'));
  }


  $breadcrumb->add(NAVBAR_TITLE_1_ACCOUNT_HISTORY, xarModURL('commerce','user',(FILENAME_ACCOUNT, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2_ACCOUNT_HISTORY, xarModURL('commerce','user',(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));

 require(DIR_WS_INCLUDES . 'header.php');

 $module_content=array();
  if (($orders_total = xtc_count_customer_orders()) > 0) {
    $history_query_raw = "select o.orders_id, o.date_purchased, o.delivery_name, o.billing_name, ot.text as order_total, s.orders_status_name from " . TABLE_ORDERS . " o, " . TABLE_ORDERS_TOTAL . " ot, " . TABLE_ORDERS_STATUS . " s where o.customers_id = '" . (int)$_SESSION['customer_id'] . "' and o.orders_id = ot.orders_id and ot.class = 'ot_total' and o.orders_status = s.orders_status_id and s.language_id = '" . (int)$_SESSION['languages_id'] . "' order by orders_id DESC";
    $history_split = new splitPageResults($history_query_raw, $_GET['page'], MAX_DISPLAY_ORDER_HISTORY);
    $history_query = new xenQuery($history_split->sql_query);

      $q = new xenQuery();
      $q->run();
    while ($history = $q->output()) {
      $products_query = new xenQuery("select count(*) as count from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . $history['orders_id'] . "'");
      $q = new xenQuery();
      $q->run();
      $products = $q->output();

      if (xarModAPIFunc('commerce','user','not_null',array('arg' => $history['delivery_name']))) {
        $order_type = TEXT_ORDER_SHIPPED_TO;
        $order_name = $history['delivery_name'];
      } else {
        $order_type = TEXT_ORDER_BILLED_TO;
        $order_name = $history['billing_name'];
      }
      $module_content[]=array(
                            'ORDER_ID'=>$history['orders_id'],
                            'ORDER_STATUS'=>$history['orders_status_name'],
                            'ORDER_DATE'=>xarModAPIFunc('commerce','user','date_long',array('raw_date' =>$history['date_purchased'])),
                            'ORDER_PRODUCTS'=>$products['count'],
                            'ORDER_TOTAL'=>strip_tags($history['order_total']),
                            'ORDER_BUTTON'=>'<a href="' . xarModURL('commerce','user',(FILENAME_ACCOUNT_HISTORY_INFO, 'page=' . $HTTP_GET_VARS['page'] . '&order_id=' . $history['orders_id'], 'SSL') . '">' .
  xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'small_view.gif'),'alt' => SMALL_IMAGE_BUTTON_VIEW);
                            </a>');

    }
  }

  if ($orders_total > 0) {
  $data['SPLIT_BAR'] = '
          <tr>
            <td class="smallText" valign="top">'. $history_split->display_count(TEXT_DISPLAY_NUMBER_OF_ORDERS).'</td>
            <td class="smallText" align="right">'. TEXT_RESULT_PAGE . ' ' . $history_split->display_links(MAX_DISPLAY_PAGE_LINKS, xtc_get_all_get_params(array('page', 'info', 'x', 'y'))).'</td>
          </tr>';

  }
  $data['order_content'] = $module_content;
  $data['language'] = $_SESSION['language'];
  $data['BUTTON_BACK'] = '<a href="' . xarModURL('commerce','user',(FILENAME_ACCOUNT, '', 'SSL') . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_back.gif'),'alt' => IMAGE_BUTTON_BACK); . '</a>';
  $smarty->caching = 0;
  return data;
  ?>
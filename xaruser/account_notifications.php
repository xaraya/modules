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
  require_once(DIR_FS_INC . 'xtc_draw_checkbox_field.inc.php');
  require_once(DIR_FS_INC . 'xtc_draw_selection_field.inc.php');

  if (!isset($_SESSION['customer_id'])) {

    xarRedirectResponse(xarModURL('commerce','user','login', '', 'SSL'));
  }


  $global_query = new xenQuery("select global_product_notifications from " . TABLE_CUSTOMERS_INFO . " where customers_info_id = '" . (int)$_SESSION['customer_id'] . "'");
      $q = new xenQuery();
      $q->run();
  $global = $q->output();

  if (isset($_POST['action']) && ($_POST['action'] == 'process')) {
    if (isset($_POST['product_global']) && is_numeric($_POST['product_global'])) {
      $product_global = xtc_db_prepare_input($_POST['product_global']);
    } else {
      $product_global = '0';
    }

    (array)$products = $_POST['products'];

    if ($product_global != $global['global_product_notifications']) {
      $product_global = (($global['global_product_notifications'] == '1') ? '0' : '1');

      new xenQuery("update " . TABLE_CUSTOMERS_INFO . " set global_product_notifications = '" . (int)$product_global . "' where customers_info_id = '" . (int)$_SESSION['customer_id'] . "'");
    } elseif (sizeof($products) > 0) {
      $products_parsed = array();
      for ($i=0, $n=sizeof($products); $i<$n; $i++) {
        if (is_numeric($products[$i])) {
          $products_parsed[] = $products[$i];
        }
      }

      if (sizeof($products_parsed) > 0) {
        $check_query = new xenQuery("select count(*) as total from " . TABLE_PRODUCTS_NOTIFICATIONS . " where customers_id = '" . (int)$_SESSION['customer_id'] . "' and products_id not in (" . implode(',', $products_parsed) . ")");
      $q = new xenQuery();
      $q->run();
        $check = $q->output();

        if ($check['total'] > 0) {
          new xenQuery("delete from " . TABLE_PRODUCTS_NOTIFICATIONS . " where customers_id = '" . (int)$_SESSION['customer_id'] . "' and products_id not in (" . implode(',', $products_parsed) . ")");
        }
      }
    } else {
      $check_query = new xenQuery("select count(*) as total from " . TABLE_PRODUCTS_NOTIFICATIONS . " where customers_id = '" . (int)$_SESSION['customer_id'] . "'");
      $q = new xenQuery();
      $q->run();
      $check = $q->output();

      if ($check['total'] > 0) {
        new xenQuery("delete from " . TABLE_PRODUCTS_NOTIFICATIONS . " where customers_id = '" . (int)$_SESSION['customer_id'] . "'");
      }
    }

    $messageStack->add_session('account', SUCCESS_NOTIFICATIONS_UPDATED, 'success');

    xarRedirectResponse(xarModURL('commerce','user',(FILENAME_ACCOUNT, '', 'SSL'));
  }

  $breadcrumb->add(NAVBAR_TITLE_1_ACCOUNT_NOTIFICATIONS, xarModURL('commerce','user',(FILENAME_ACCOUNT, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2_ACCOUNT_NOTIFICATIONS, xarModURL('commerce','user',(FILENAME_ACCOUNT_NOTIFICATIONS, '', 'SSL'));

 require(DIR_WS_INCLUDES . 'header.php');



$data['CHECKBOX_GLOBAL'] = xtc_draw_checkbox_field('product_global', '1', (($global['global_product_notifications'] == '1') ? true : false), 'onclick="checkBox(\'product_global\')"');
if ($global['global_product_notifications'] != '1') {
$data['GLOBAL_NOTIFICATION'] = '0';
} else {
$data['GLOBAL_NOTIFICATION'] = '1';
}
  if ($global['global_product_notifications'] != '1') {

    $products_check_query = new xenQuery("select count(*) as total from " . TABLE_PRODUCTS_NOTIFICATIONS . " where customers_id = '" . (int)$_SESSION['customer_id'] . "'");
      $q = new xenQuery();
      $q->run();
    $products_check = $q->output();
    if ($products_check['total'] > 0) {

      $counter = 0;
      $notifications_products='<table width="100%" border="0" cellspacing="0" cellpadding="0">';
      $products_query = new xenQuery("select pd.products_id, pd.products_name from " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_NOTIFICATIONS . " pn where pn.customers_id = '" . (int)$_SESSION['customer_id'] . "' and pn.products_id = pd.products_id and pd.language_id = '" . (int)$_SESSION['languages_id'] . "' order by pd.products_name");
      $q = new xenQuery();
      $q->run();
      while ($products = $q->output()) {
      $notifications_products.= '

                  <tr class="moduleRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="checkBox(\'products['.$counter.']\')">
                    <td class="main" width="30">'.xtc_draw_checkbox_field('products[' . $counter . ']', $products['products_id'], true, 'onclick="checkBox(\'products[' . $counter . ']\')"').'</td>
                    <td class="main"><b>'.$products['products_name'].'</b></td>
                  </tr> ';

        $counter++;
      }
      $notifications_products.= '</table>';
      $data['PRODUCTS_NOTIFICATION'] = $notifications_products;
    } else {

    }

  }


  $data['language'] = $_SESSION['language'];
  $data['BUTTON_BACK'] = '<a href="' . xarModURL('commerce','user',(FILENAME_ACCOUNT, '', 'SSL') . '">' .
  . '</a>';
xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_back.gif'),
        'alt' => IMAGE_BUTTON_BACK);
  $data['BUTTON_CONTINUE'] = <input type="image" src="#xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_continue.gif')#" border="0" alt=IMAGE_BUTTON_CONTINUE>;
  $smarty->caching = 0;
  return data;
  ?>
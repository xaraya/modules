<?php
// ----------------------------------------------------------------------
// Copyright (C) 2004: Marc Lutolf (marcinmilan@xaraya.com)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003 XT-Commerce
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
//   based on Third Party contribution:
//   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist
// ----------------------------------------------------------------------
  // write customers status in session
  if (isset($_SESSION['customer_id'])) {
    $customers_status_query_1 = new xenQuery("SELECT customers_status FROM " . TABLE_CUSTOMERS . " WHERE customers_id = '" . $_SESSION['customer_id'] . "'");
      $q = new xenQuery();
      $q->run();
    $customers_status_value_1 = $q->output();

    $customers_status_query = new xenQuery("SELECT
                                                customers_status_name,
                                                customers_status_discount,
                                                customers_status_public,
                                                customers_status_image,
                                                customers_status_ot_discount_flag,
                                                customers_status_ot_discount,
                                                customers_status_graduated_prices,
                                                customers_status_show_price,
                                                customers_status_show_price_tax,
                                                customers_status_add_tax_ot,
                                                customers_status_payment_unallowed,
                                                customers_status_shipping_unallowed,
                                                customers_status_discount_attributes
                                            FROM
                                                " . TABLE_CUSTOMERS_STATUS . "
                                            WHERE
                                                customers_status_id = '" . $customers_status_value_1['customers_status'] . "' AND language_id = '" . $_SESSION['languages_id'] . "'");

      $q = new xenQuery();
      $q->run();
    $customers_status_value = $q->output();

    $_SESSION['customers_status'] = array();
    $_SESSION['customers_status']= array(
      'customers_status_id' => $customers_status_value_1['customers_status'],
      'customers_status_name' => $customers_status_value['customers_status_name'],
      'customers_status_image' => $customers_status_value['customers_status_image'],
      'customers_status_public' => $customers_status_value['customers_status_public'],
      'customers_status_discount' => $customers_status_value['customers_status_discount'],
      'customers_status_ot_discount_flag' => $customers_status_value['customers_status_ot_discount_flag'],
      'customers_status_ot_discount' => $customers_status_value['customers_status_ot_discount'],
      'customers_status_graduated_prices' => $customers_status_value['customers_status_graduated_prices'],
      'customers_status_show_price' => $customers_status_value['customers_status_show_price'],
      'customers_status_show_price_tax' => $customers_status_value['customers_status_show_price_tax'],
      'customers_status_add_tax_ot' => $customers_status_value['customers_status_add_tax_ot'],
      'customers_status_payment_unallowed' => $customers_status_value['customers_status_payment_unallowed'],
      'customers_status_shipping_unallowed' => $customers_status_value['customers_status_shipping_unallowed'],
      'customers_status_discount_attributes' => $customers_status_value['customers_status_discount_attributes']
    );
  } else {
    $customers_status_query = new xenQuery("SELECT
                                                customers_status_name,
                                                customers_status_discount,
                                                customers_status_public,
                                                customers_status_image,
                                                customers_status_ot_discount_flag,
                                                customers_status_ot_discount,
                                                customers_status_graduated_prices,
                                                customers_status_show_price,
                                                customers_status_show_price_tax,
                                                customers_status_add_tax_ot,
                                                customers_status_payment_unallowed,
                                                customers_status_shipping_unallowed,
                                                customers_status_discount_attributes
                                            FROM
                                                " . TABLE_CUSTOMERS_STATUS . "
                                            WHERE
                                                customers_status_id = '" . DEFAULT_CUSTOMERS_STATUS_ID_GUEST . "' AND language_id = '" . $_SESSION['languages_id'] . "'");
      $q = new xenQuery();
      $q->run();
    $customers_status_value = $q->output();

    $_SESSION['customers_status'] = array();
    $_SESSION['customers_status']= array(
      'customers_status_id' => DEFAULT_CUSTOMERS_STATUS_ID_GUEST,
      'customers_status_name' => $customers_status_value['customers_status_name'],
      'customers_status_image' => $customers_status_value['customers_status_image'],
      'customers_status_discount' => $customers_status_value['customers_status_discount'],
      'customers_status_public' => $customers_status_value['customers_status_public'],
      'customers_status_ot_discount_flag' => $customers_status_value['customers_status_ot_discount_flag'],
      'customers_status_ot_discount' => $customers_status_value['customers_status_ot_discount'],
      'customers_status_graduated_prices' => $customers_status_value['customers_status_graduated_prices'],
      'customers_status_show_price' => $customers_status_value['customers_status_show_price'],
      'customers_status_show_price_tax' => $customers_status_value['customers_status_show_price_tax'],
      'customers_status_add_tax_ot' => $customers_status_value['customers_status_add_tax_ot'],
      'customers_status_payment_unallowed' => $customers_status_value['customers_status_payment_unallowed'],
      'customers_status_shipping_unallowed' => $customers_status_value['customers_status_shipping_unallowed'],
      'customers_status_discount_attributes' => $customers_status_value['customers_status_discount_attributes']
    );
  }

?>
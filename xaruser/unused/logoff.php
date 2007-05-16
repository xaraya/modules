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


  $breadcrumb->add(NAVBAR_TITLE_LOGOFF);

  xtc_session_destroy();

  unset($_SESSION['customer_id']);
  unset($_SESSION['customer_default_address_id']);
  unset($_SESSION['customer_first_name']);
  unset($_SESSION['customer_country_id']);
  unset($_SESSION['customer_zone_id']);
  unset($_SESSION['comments']);
  unset($_SESSION['user_info']);
  unset($_SESSION['customers_status']);
  unset($_SESSION['selected_box']);
  unset($_SESSION['navigation']);
  unset($_SESSION['shipping']);
  unset($_SESSION['payment']);
  $_SESSION['cart']->reset();
  // write customers status guest in session again
  require(DIR_WS_INCLUDES . 'write_customers_status.php');

 require(DIR_WS_INCLUDES . 'header.php');

  $data['BUTTON_CONTINUE'] = '<a href="' . xarModURL('commerce','user','default') . '">' .
  xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_continue.gif'),
        'alt' => IMAGE_BUTTON_CONTINUE);
. '</a>';
  $data['language'] =  $_SESSION['language'];


  $smarty->caching = 0;
  return data;
  ?>
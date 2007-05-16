<?php
// ----------------------------------------------------------------------
// Copyright (C) 2004: Marc Lutolf (marcinmilan@xaraya.com)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003 XT-Commerce
//   guest account idea by Ingo T. <xIngox@web.de>
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------

   // create smarty elements
//  $smarty = new Smarty;
  // include boxes
  require(DIR_WS_INCLUDES.'boxes.php');

  // include needed functions
  require_once(DIR_FS_INC . 'xtc_draw_password_field.inc.php');
  require_once(DIR_FS_INC . 'xtc_validate_password.inc.php');
  require_once(DIR_FS_INC . 'xtc_array_to_string.inc.php');

  // redirect the customer to a friendly cookie-must-be-enabled page if cookies are disabled (or the session has not started)
  if ($session_started == false) {
    xarRedirectResponse(xarModURL('commerce','user',(FILENAME_COOKIE_USAGE));
  }

  if (isset($_GET['action']) && ($_GET['action'] == 'process')) {
    $email_address = xtc_db_prepare_input($_POST['email_address']);
    $password = xtc_db_prepare_input($_POST['password']);

    // Check if email exists
    $check_customer_query = new xenQuery("select customers_id, customers_firstname,customers_lastname, customers_gender, customers_password, customers_email_address, customers_default_address_id from " . TABLE_CUSTOMERS . " where customers_email_address = '" . xtc_db_input($email_address) . "'");
    if (!$check_customer_query->getrows()) {
      $_GET['login'] = 'fail';
    } else {
      $q = new xenQuery();
      if(!$q->run()) return;
      $check_customer = $q->output();
      // Check that password is good
      if (!xtc_validate_password($password, $check_customer['customers_password'])) {
        $_GET['login'] = 'fail';
      } else {
        if (SESSION_RECREATE == 'True') {
          xtc_session_recreate();
        }

        $check_country_query = new xenQuery("select entry_country_id, entry_zone_id from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $check_customer['customers_id'] . "' and address_book_id = '" . $check_customer['customers_default_address_id'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
        $check_country = $q->output();

        $_SESSION['customer_gender'] = $check_customer['customers_gender'];
        $_SESSION['customer_last_name'] = $check_customer['customers_lastname'];
        $_SESSION['customer_id'] = $check_customer['customers_id'];
        $_SESSION['customer_default_address_id'] = $check_customer['customers_default_address_id'];
        $_SESSION['customer_first_name'] = $check_customer['customers_firstname'];
        $_SESSION['customer_country_id'] = $check_country['entry_country_id'];
        $_SESSION['customer_zone_id'] = $check_country['entry_zone_id'];

        $date_now = date('Ymd');
        new xenQuery("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_of_last_logon = now(), customers_info_number_of_logons = customers_info_number_of_logons+1 where customers_info_id = '" . (int)$_SESSION['customer_id'] . "'");

        // restore cart contents
        $_SESSION['cart']->restore_contents();

        if (sizeof($_SESSION['navigation']->snapshot) > 0) {
          $origin_href = xarModURL('commerce','user',($_SESSION['navigation']->snapshot['page'], xtc_array_to_string($_SESSION['navigation']->snapshot['get'], array(xtc_session_name())), $_SESSION['navigation']->snapshot['mode']);
          $_SESSION['navigation']->clear_snapshot();
          xtc_redirect($origin_href);
        } else {
          xarRedirectResponse(xarModURL('commerce','user','default'));
        }
      }
    }
  }



  $breadcrumb->add(NAVBAR_TITLE_LOGIN, xarModURL('commerce','user','login'));
 require(DIR_WS_INCLUDES . 'header.php');



$data['info_message'] = $info_message;
$data['account_option'] = ACCOUNT_OPTIONS;
$data['BUTTON_NEW_ACCOUNT'] = '<a href="' . xarModURL('commerce','user','create_account', '', 'SSL') . '">' .
xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_continue.gif'),
        'alt' => IMAGE_BUTTON_CONTINUE);
. '</a>';
$data['BUTTON_LOGIN'] =
<input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_login.gif')#" border="0" alt=IMAGE_BUTTON_LOGIN>;
$data['BUTTON_GUEST'] = '<a href="' . xarModURL('commerce','user',(FILENAME_CREATE_GUEST_ACCOUNT, '', 'SSL') . '">' .
xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_continue.gif'),
        'alt' => IMAGE_BUTTON_CONTINUE);
. '</a>';
$data['FORM_ACTION'] = xarModURL('commerce','user','login', 'action=process', 'SSL');
$data['INPUT_MAIL'] = xtc_draw_input_field('email_address');
$data['INPUT_PASSWORD'] = xtc_draw_password_field('password');
$data['LINK_LOST_PASSWORD'] = xarModURL('commerce','user',(FILENAME_PASSWORD_FORGOTTEN, '', 'SSL');



  $data['language'] =  $_SESSION['language'];
  $smarty->caching = 0;
  return data;
?>
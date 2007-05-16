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
  require_once(DIR_FS_INC . 'xtc_draw_password_field.inc.php');
  require_once(DIR_FS_INC . 'xtc_validate_password.inc.php');
  require_once(DIR_FS_INC . 'xtc_encrypt_password.inc.php');

  if (!isset($_SESSION['customer_id'])) {

    xarRedirectResponse(xarModURL('commerce','user','login', '', 'SSL'));
  }

  if (isset($_POST['action']) && ($_POST['action'] == 'process')) {
    $password_current = xtc_db_prepare_input($_POST['password_current']);
    $password_new = xtc_db_prepare_input($_POST['password_new']);
    $password_confirmation = xtc_db_prepare_input($_POST['password_confirmation']);

    $error = false;

    if (strlen($password_current) < ENTRY_PASSWORD_MIN_LENGTH) {
      $error = true;

      $messageStack->add('account_password', ENTRY_PASSWORD_CURRENT_ERROR);
    } elseif (strlen($password_new) < ENTRY_PASSWORD_MIN_LENGTH) {
      $error = true;

      $messageStack->add('account_password', ENTRY_PASSWORD_NEW_ERROR);
    } elseif ($password_new != $password_confirmation) {
      $error = true;

      $messageStack->add('account_password', ENTRY_PASSWORD_NEW_ERROR_NOT_MATCHING);
    }

    if ($error == false) {
      $check_customer_query = new xenQuery("select customers_password from " . TABLE_CUSTOMERS . " where customers_id = '" . (int)$_SESSION['customer_id'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
      $check_customer = $q->output();

      if (xtc_validate_password($password_current, $check_customer['customers_password'])) {
        new xenQuery("update " . TABLE_CUSTOMERS . " set customers_password = '" . xtc_encrypt_password($password_new) . "' where customers_id = '" . (int)$_SESSION['customer_id'] . "'");

        new xenQuery("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_account_last_modified = now() where customers_info_id = '" . (int)$_SESSION['customer_id'] . "'");

        $messageStack->add_session('account', SUCCESS_PASSWORD_UPDATED, 'success');

        xarRedirectResponse(xarModURL('commerce','user',(FILENAME_ACCOUNT, '', 'SSL'));
      } else {
        $error = true;

        $messageStack->add('account_password', ERROR_CURRENT_PASSWORD_NOT_MATCHING);
      }
    }
  }

  $breadcrumb->add(NAVBAR_TITLE_1_ACCOUNT_PASSWORD, xarModURL('commerce','user','account'));
  $breadcrumb->add(NAVBAR_TITLE_2_ACCOUNT_PASSWORD, xarModURL('commerce','user','account_password'));

 require(DIR_WS_INCLUDES . 'header.php');

  if ($messageStack->size('account_password') > 0) {
  $data['error'] = $messageStack->output('account_password';

  }
  $data['INPUT_ACTUAL'] = xtc_draw_password_field('password_current') . '&#160;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_PASSWORD_CURRENT_TEXT)) ? '<span class="inputRequirement">' . ENTRY_PASSWORD_CURRENT_TEXT . '</span>': '');
  $data['INPUT_NEW'] = xtc_draw_password_field('password_new') . '&#160;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_PASSWORD_NEW_TEXT)) ? '<span class="inputRequirement">' . ENTRY_PASSWORD_NEW_TEXT . '</span>': '');
  $data['INPUT_CONFIRM'] = xtc_draw_password_field('password_confirmation') . '&#160;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_PASSWORD_CONFIRMATION_TEXT)) ? '<span class="inputRequirement">' . ENTRY_PASSWORD_CONFIRMATION_TEXT . '</span>': '');

 $data['BUTTON_BACK'] = '<a href="' . xarModURL('commerce','user',(FILENAME_ACCOUNT, '', 'SSL') . '">' .
 . '</a>';
xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_back.gif'),
        'alt' => IMAGE_BUTTON_BACK);
 $data['BUTTON_SUBMIT'] = <input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_continue.gif')#" border="0" alt=IMAGE_BUTTON_CONTINUE>;

  $data['language'] = $_SESSION['language'];

  $smarty->caching = 0;
  return data;
  ?>
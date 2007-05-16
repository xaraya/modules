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

//      $smarty = new Smarty;
  // include boxes
  require(DIR_WS_INCLUDES.'boxes.php');

  // include needed functions
  require_once(DIR_FS_INC . 'xtc_encrypt_password.inc.php');
  require_once(DIR_WS_CLASSES.'class.phpmailer.php');
  require_once(DIR_FS_INC . 'xtc_php_mail.inc.php');



  if (isset($_GET['action']) && ($_GET['action'] == 'process')) {
    $check_customer_query = new xenQuery("select customers_firstname, customers_lastname, customers_password, customers_id from " . TABLE_CUSTOMERS . " where customers_email_address = '" . $_POST['email_address'] . "' and account_type!=1");
    if ($check_customer_query->getrows()) {
      $q = new xenQuery();
      if(!$q->run()) return;
      $check_customer = $q->output();
      // Crypted password mods - create a new password, update the database and mail it to them
      $newpass = xtc_create_random_value(ENTRY_PASSWORD_MIN_LENGTH);
      $crypted_password = xtc_encrypt_password($newpass);

      new xenQuery("update " . TABLE_CUSTOMERS . " set customers_password = '" . $crypted_password . "' where customers_id = '" . $check_customer['customers_id'] . "'");

        // assign language to template for caching
    $data['language'] =  $_SESSION['language'];
    $data['tpl_path'] = 'templates/'.CURRENT_TEMPLATE.'/';

        // assign vars
        $data['EMAIL'] = $_POST['email_address'];
        $data['PASSWORD'] = $newpass;
        $data['FIRSTNAME'] = $check_customer['customers_firstname'];
        $data['LASTNAME'] = $check_customer['customers_lastname'];
        // dont allow cache
    $smarty->caching = false;

    // create mails
    $html_mail=$smarty->fetch(CURRENT_TEMPLATE.'/mail/change_password_mail.html');
    $txt_mail=$smarty->fetch(CURRENT_TEMPLATE.'/mail/change_password_mail.txt');

      xtc_php_mail(EMAIL_SUPPORT_ADDRESS,EMAIL_SUPPORT_NAME , $_POST['email_address'], $check_customer['customers_firstname'] . " " . $check_customer['customers_lastname'], EMAIL_SUPPORT_FORWARDING_STRING, EMAIL_SUPPORT_REPLY_ADDRESS, EMAIL_SUPPORT_REPLY_ADDRESS_NAME, '', '', EMAIL_SUPPORT_SUBJECT, $html_mail, $txt_mail);

      if (!isset($mail_error)) {
          xarRedirectResponse(xarModURL('commerce','user','login', 'info_message=' . urlencode(TEXT_PASSWORD_SENT), 'SSL', true, false));
      }
      else {
          echo $mail_error;
      }
    } else {
      xarRedirectResponse(xarModURL('commerce','user',(FILENAME_PASSWORD_FORGOTTEN, 'email=nonexistent', 'SSL'));
    }
  } else {
    $breadcrumb->add(NAVBAR_TITLE_1_PASSWORD_FORGOTTEN, xarModURL('commerce','user','login'));
    $breadcrumb->add(NAVBAR_TITLE_2_PASSWORD_FORGOTTEN, xarModURL('commerce','user','filenam_password_forgotten'));

 include(DIR_WS_INCLUDES . 'header.php');

 $data['INPUT_EMAIL'] = xtc_draw_input_field('email_address', '', 'maxlength="96"');
 $data['BUTTON_SUBMIT'] = <input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_continue.gif')#" border="0" alt=IMAGE_BUTTON_CONTINUE>;

    if (isset($_GET['email']) && ($_GET['email'] == 'nonexistent')) {
    $data['error'] = '1';
    }

  }


  $data['language'] =  $_SESSION['language'];


  // set cache ID
  if (USE_CACHE=='false') {
  $smarty->caching = 0;
  return data;
  } else {
  $smarty->caching = 1;
  $smarty->cache_lifetime=CACHE_LIFETIME;
  $smarty->cache_modified_check=CACHE_CHECK;
  $cache_id = $_SESSION['language'];
  return data;
  }
  ?>
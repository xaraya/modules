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



  $newsletter_query = new xenQuery("select customers_newsletter from " . TABLE_CUSTOMERS . " where customers_id = '" . (int)$_SESSION['customer_id'] . "'");
      $q = new xenQuery();
      $q->run();
  $newsletter = $q->output();

  if (isset($_POST['action']) && ($_POST['action'] == 'process')) {
    if (isset($_POST['newsletter_general']) && is_numeric($_POST['newsletter_general'])) {
      $newsletter_general = xtc_db_prepare_input($_POST['newsletter_general']);
    } else {
      $newsletter_general = '0';
    }

    if ($newsletter_general != $newsletter['customers_newsletter']) {
      $newsletter_general = (($newsletter['customers_newsletter'] == '1') ? '0' : '1');

      new xenQuery("update " . TABLE_CUSTOMERS . " set customers_newsletter = '" . (int)$newsletter_general . "' where customers_id = '" . (int)$_SESSION['customer_id'] . "'");
    }

    $messageStack->add_session('account', SUCCESS_NEWSLETTER_UPDATED, 'success');

    xarRedirectResponse(xarModURL('commerce','user',(FILENAME_ACCOUNT, '', 'SSL'));
  }

  $breadcrumb->add(NAVBAR_TITLE_1_ACCOUNT_NEWSLETTERS, xarModURL('commerce','user',(FILENAME_ACCOUNT, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2_ACCOUNT_NEWSLETTERS, xarModURL('commerce','user',(FILENAME_ACCOUNT_NEWSLETTERS, '', 'SSL'));

 require(DIR_WS_INCLUDES . 'header.php');

  $data['CHECKBOX'] = xtc_draw_checkbox_field('newsletter_general', '1', (($newsletter['customers_newsletter'] == '1') ? true : false), 'onclick="checkBox(\'newsletter_general\')"');
  $data['language'] = $_SESSION['language'];
  $data['BUTTON_BACK'] = '<a href="' . xarModURL('commerce','user',(FILENAME_ACCOUNT, '', 'SSL') . '">' .
xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_back.gif'),'alt' => IMAGE_BUTTON_BACK);
  . '</a>';
  $data['BUTTON_CONTINUE'] = <input type="image" src="#xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_continue.gif')#" border="0" alt=IMAGE_BUTTON_CONTINUE>);
  $smarty->caching = 0;
  return data;
  ?>
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
  require_once(DIR_FS_INC . 'xtc_count_customer_address_book_entries.inc.php');

  if (!isset($_SESSION['customer_id'])) {

    xarRedirectResponse(xarModURL('commerce','user','login', '', 'SSL'));
  }


  $breadcrumb->add(NAVBAR_TITLE_1_ADDRESS_BOOK, xarModURL('commerce','user',(FILENAME_ACCOUNT, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2_ADDRESS_BOOK, xarModURL('commerce','user',(FILENAME_ADDRESS_BOOK, '', 'SSL'));

  require(DIR_WS_INCLUDES . 'header.php');



  if ($messageStack->size('addressbook') > 0) {
  $data['error'] = $messageStack->output('addressbook');

  }
  $data['ADDRESS_DEFAULT'] = xarModAPIFunc('commerce','user','address_label',array(
    'address_format_id' =>$_SESSION['customer_id'],
    'address' =>$_SESSION['customer_default_address_id'],
    'html' =>true,
    'boln' =>' ',
    'eoln' =>'<br>'));

  $addresses_data=array();
  $addresses_query = new xenQuery("select address_book_id, entry_firstname as firstname, entry_lastname as lastname, entry_company as company, entry_street_address as street_address, entry_suburb as suburb, entry_city as city, entry_postcode as postcode, entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$_SESSION['customer_id'] . "' order by firstname, lastname");
      $q = new xenQuery();
      $q->run();
  while ($addresses = $q->output()) {
    $format_id = xtc_get_address_format_id($addresses['country_id']);
     if ($addresses['address_book_id'] == $_SESSION['customer_default_address_id']) {
     $primary = 1;
     } else {
     $primary =0;
     }
    $addresses_data[]=array(
                          'NAME'=> $addresses['firstname'] . ' ' . $addresses['lastname'],
                          'BUTTON_EDIT'=> '<a href="' . xarModURL('commerce','user',(FILENAME_ADDRESS_BOOK_PROCESS, 'edit=' . $addresses['address_book_id'], 'SSL') . '">' .
xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'small_edit.gif'),
        'alt' => SMALL_IMAGE_BUTTON_EDIT);
                          . '</a>',
                          'BUTTON_DELETE'=> '<a href="' . xarModURL('commerce','user',(FILENAME_ADDRESS_BOOK_PROCESS, 'delete=' . $addresses['address_book_id'], 'SSL') . '">' .
xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'small_delete.gif'),
        'alt' => SMALL_IMAGE_BUTTON_DELETE);
                          . '</a>',
                          'ADDRESS'=> xarModAPIFunc('commerce','user','address_format',array(
                                        'address_format_id' =>$format_id,
                                        'address' =>$addresses,
                                        'html' =>true,
                                        'boln' =>' ',
                                        'eoln' =>'<br>')),
                          'PRIMARY'=> $primary);


  }
  $data['addresses_data'] = $addresses_data;

  $data['BUTTON_BACK'] = '<a href="' . xarModURL('commerce','user',(FILENAME_ACCOUNT, '', 'SSL') . '">' .
xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_back.gif'),
        'alt' => IMAGE_BUTTON_BACK);
  . '</a>';

  if (xtc_count_customer_address_book_entries() < MAX_ADDRESS_BOOK_ENTRIES) {


  $data['BUTTON_NEW'] = '<a href="' . xarModURL('commerce','user',(FILENAME_ADDRESS_BOOK_PROCESS, '', 'SSL') . '">' .
  xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_add_address.gif'),
        'alt' => IMAGE_BUTTON_ADD_ADDRESS);
. '</a>';
  }

  $data['ADDRESS_COUNT'] = sprintf(TEXT_MAXIMUM_ENTRIES, MAX_ADDRESS_BOOK_ENTRIES);

  $data['language'] =  $_SESSION['language'];
  $smarty->caching = 0;
  return data;
  ?>
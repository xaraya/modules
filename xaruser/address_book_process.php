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
  require_once(DIR_FS_INC . 'xtc_draw_radio_field.inc.php');
  require_once(DIR_FS_INC . 'xtc_count_customer_address_book_entries.inc.php');

  if (!isset($_SESSION['customer_id'])) {

    xarRedirectResponse(xarModURL('commerce','user','login', '', 'SSL'));
  }


  if (isset($_GET['action']) && ($_GET['action'] == 'deleteconfirm') && isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    new xenQuery("delete from " . TABLE_ADDRESS_BOOK . " where address_book_id = '" . (int)$_GET['delete'] . "' and customers_id = '" . (int)$_SESSION['customer_id'] . "'");

    $messageStack->add_session('addressbook', SUCCESS_ADDRESS_BOOK_ENTRY_DELETED, 'success');

    xarRedirectResponse(xarModURL('commerce','user',(FILENAME_ADDRESS_BOOK, '', 'SSL'));
  }

  // error checking when updating or adding an entry
  $process = false;
  if (isset($_POST['action']) && (($_POST['action'] == 'process') || ($_POST['action'] == 'update'))) {
    $process = true;
    $error = false;

    if (ACCOUNT_GENDER == 'true') $gender = xtc_db_prepare_input($_POST['gender']);
    if (ACCOUNT_COMPANY == 'true') $company = xtc_db_prepare_input($_POST['company']);
    $firstname = xtc_db_prepare_input($_POST['firstname']);
    $lastname = xtc_db_prepare_input($_POST['lastname']);
    $street_address = xtc_db_prepare_input($_POST['street_address']);
    if (ACCOUNT_SUBURB == 'true') $suburb = xtc_db_prepare_input($_POST['suburb']);
    $postcode = xtc_db_prepare_input($_POST['postcode']);
    $city = xtc_db_prepare_input($_POST['city']);
    $country = xtc_db_prepare_input($_POST['country']);
    if (ACCOUNT_STATE == 'true') {
      $zone_id = xtc_db_prepare_input($_POST['zone_id']);
      $state = xtc_db_prepare_input($_POST['state']);
    }

    if (ACCOUNT_GENDER == 'true') {
      if ( ($gender != 'm') && ($gender != 'f') ) {
        $error = true;

        $messageStack->add('addressbook', ENTRY_GENDER_ERROR);
      }
    }

    if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
      $error = true;

      $messageStack->add('addressbook', ENTRY_FIRST_NAME_ERROR);
    }

    if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
      $error = true;

      $messageStack->add('addressbook', ENTRY_LAST_NAME_ERROR);
    }

    if (strlen($street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
      $error = true;

      $messageStack->add('addressbook', ENTRY_STREET_ADDRESS_ERROR);
    }

    if (strlen($postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
      $error = true;

      $messageStack->add('addressbook', ENTRY_POST_CODE_ERROR);
    }

    if (strlen($city) < ENTRY_CITY_MIN_LENGTH) {
      $error = true;

      $messageStack->add('addressbook', ENTRY_CITY_ERROR);
    }

    if (is_numeric($country) == false) {
      $error = true;

      $messageStack->add('addressbook', ENTRY_COUNTRY_ERROR);
    }

    if (ACCOUNT_STATE == 'true') {
      $zone_id = 0;
      $check_query = new xenQuery("select count(*) as total from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country . "'");
      $q = new xenQuery();
      $q->run();
      $check = $q->output();
      $entry_state_has_zones = ($check['total'] > 0);
      if ($entry_state_has_zones == true) {
        $zone_query = new xenQuery("select distinct zone_id from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country . "' and (zone_name like '" . xtc_db_input($state) . "%' or zone_code like '%" . xtc_db_input($state) . "%')");
        if ($zone_query->getrows() == 1) {
      $q = new xenQuery();
      $q->run();
          $zone = $q->output();
          $zone_id = $zone['zone_id'];
        } else {
          $error = true;

          $messageStack->add('addressbook', ENTRY_STATE_ERROR_SELECT);
        }
      } else {
        if (strlen($state) < ENTRY_STATE_MIN_LENGTH) {
          $error = true;

          $messageStack->add('addressbook', ENTRY_STATE_ERROR);
        }
      }
    }

    if ($error == false) {
      $q->addfield('entry_firstname',$firstname);
                              $q->addfield('entry_lastname',$lastname);
                              $q->addfield('entry_street_address',$street_address);
                              $q->addfield('entry_postcode',$postcode);
                              $q->addfield('entry_city',$city);
                              $q->addfield('entry_country_id',(int)$country);

      if (ACCOUNT_GENDER == 'true') $q->addfield('entry_gender',$gender);
      if (ACCOUNT_COMPANY == 'true') $q->addfield('entry_company',$company);
      if (ACCOUNT_SUBURB == 'true') $q->addfield('entry_suburb',$suburb);
      if (ACCOUNT_STATE == 'true') {
        if ($zone_id > 0) {
          $q->addfield('entry_zone_id',(int)$zone_id);
          $q->addfield('entry_state','');
        } else {
          $q->addfield('entry_zone_id','0');
          $q->addfield('entry_state',$state);
        }
      }

      if ($_POST['action'] == 'update') {
        xtc_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array, 'update', "address_book_id = '" . (int)$_GET['edit'] . "' and customers_id ='" . (int)$_SESSION['customer_id'] . "'");

        // reregister session variables
        if ( (isset($_POST['primary']) && ($_POST['primary'] == 'on')) || ($_GET['edit'] == $_SESSION['customer_default_address_id']) ) {
          $_SESSION['customer_first_name'] = $firstname;
          $_SESSION['customer_country_id'] = $country_id;
          $_SESSION['customer_zone_id'] = (($zone_id > 0) ? (int)$zone_id : '0');
          $_SESSION['customer_default_address_id'] = (int)$_GET['edit'];

          $q->addfield('customers_firstname',$firstname,
                                  $q->addfield('customers_lastname',$lastname,
                                  $q->addfield('customers_default_address_id',(int)$_GET['edit']);

          if (ACCOUNT_GENDER == 'true') $q->addfield('customers_gender',$gender);

          xtc_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', "customers_id = '" . (int)$_SESSION['customer_id'] . "'");
        }
      } else {
        $q->addfield('customers_id',(int)$_SESSION['customer_id']);
        xtc_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);

        $new_address_book_id = xtc_db_insert_id();

        // reregister session variables
        if (isset($_POST['primary']) && ($_POST['primary'] == 'on')) {
          $_SESSION['customer_first_name'] = $firstname;
          $_SESSION['customer_country_id'] = $country_id;
          $_SESSION['customer_zone_id'] = (($zone_id > 0) ? (int)$zone_id : '0');
          if (isset($_POST['primary']) && ($_POST['primary'] == 'on')) $_SESSION['customer_default_address_id'] = $new_address_book_id;

          $q->addfield('customers_firstname',$firstname,
                                  $q->addfield('customers_lastname',$lastname);

          if (ACCOUNT_GENDER == 'true') $q->addfield('customers_gender',$gender);
          if (isset($_POST['primary']) && ($_POST['primary'] == 'on')) $q->addfield('customers_default_address_id',$new_address_book_id);

          xtc_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', "customers_id = '" . (int)$_SESSION['customer_id'] . "'");
        }
      }

      $messageStack->add_session('addressbook', SUCCESS_ADDRESS_BOOK_ENTRY_UPDATED, 'success');

      xarRedirectResponse(xarModURL('commerce','user',(FILENAME_ADDRESS_BOOK, '', 'SSL'));
    }
  }

  if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $entry_query = new xenQuery("select entry_gender, entry_company, entry_firstname, entry_lastname, entry_street_address, entry_suburb, entry_postcode, entry_city, entry_state, entry_zone_id, entry_country_id from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$_SESSION['customer_id'] . "' and address_book_id = '" . (int)$_GET['edit'] . "'");

    if ($entry_query->getrows() == false) {
      $messageStack->add_session('addressbook', ERROR_NONEXISTING_ADDRESS_BOOK_ENTRY);

      xarRedirectResponse(xarModURL('commerce','user',(FILENAME_ADDRESS_BOOK, '', 'SSL'));
    }

      $q = new xenQuery();
      $q->run();
    $entry = $q->output();
  } elseif (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if ($_GET['delete'] == $_SESSION['customer_default_address_id']) {
      $messageStack->add_session('addressbook', WARNING_PRIMARY_ADDRESS_DELETION, 'warning');

      xarRedirectResponse(xarModURL('commerce','user',(FILENAME_ADDRESS_BOOK, '', 'SSL'));
    } else {
      $check_query = new xenQuery("select count(*) as total from " . TABLE_ADDRESS_BOOK . " where address_book_id = '" . (int)$_GET['delete'] . "' and customers_id = '" . (int)$_SESSION['customer_id'] . "'");
      $q = new xenQuery();
      $q->run();
      $check = $q->output();

      if ($check['total'] < 1) {
        $messageStack->add_session('addressbook', ERROR_NONEXISTING_ADDRESS_BOOK_ENTRY);

        xarRedirectResponse(xarModURL('commerce','user',(FILENAME_ADDRESS_BOOK, '', 'SSL'));
      }
    }
  } else {
    $entry = array();
  }

  if (!isset($_GET['delete']) && !isset($_GET['edit'])) {
    if (xtc_count_customer_address_book_entries() >= MAX_ADDRESS_BOOK_ENTRIES) {
      $messageStack->add_session('addressbook', ERROR_ADDRESS_BOOK_FULL);

      xarRedirectResponse(xarModURL('commerce','user',(FILENAME_ADDRESS_BOOK, '', 'SSL'));
    }
  }

  $breadcrumb->add(NAVBAR_TITLE_1_ADDRESS_BOOK_PROCESS, xarModURL('commerce','user',(FILENAME_ACCOUNT, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2_ADDRESS_BOOK_PROCESS, xarModURL('commerce','user',(FILENAME_ADDRESS_BOOK, '', 'SSL'));

  if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $breadcrumb->add(NAVBAR_TITLE_MODIFY_ENTRY_ADDRESS_BOOK_PROCESS, xarModURL('commerce','user',(FILENAME_ADDRESS_BOOK_PROCESS, 'edit=' . $_GET['edit'], 'SSL'));
  } elseif (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $breadcrumb->add(NAVBAR_TITLE_DELETE_ENTRY_ADDRESS_BOOK_PROCESS, xarModURL('commerce','user',(FILENAME_ADDRESS_BOOK_PROCESS, 'delete=' . $_GET['delete'], 'SSL'));
  } else {
    $breadcrumb->add(NAVBAR_TITLE_ADD_ENTRY_ADDRESS_BOOK_PROCESS, xarModURL('commerce','user',(FILENAME_ADDRESS_BOOK_PROCESS, '', 'SSL'));
  }

 require(DIR_WS_INCLUDES . 'header.php');
 if (isset($_GET['delete']) == false) $action= xtc_draw_form('addressbook', xarModURL('commerce','user',(FILENAME_ADDRESS_BOOK_PROCESS, (isset($_GET['edit']) ? 'edit=' . $_GET['edit'] : ''), 'SSL'), 'post', 'onSubmit="return check_form(addressbook);"');

  $data['FORM_ACTION'] = $action;
  if ($messageStack->size('addressbook') > 0) {
  $data['error'] = $messageStack->output('addressbook');

  }

  if (isset($_GET['delete'])) {
  $data['delete'] = '1';
  $data['ADDRESS'] =  xarModAPIFunc('commerce','user','address_label',array(
    'address_format_id' =>$_SESSION['customer_id'],
    'address' =>$_GET['delete'],
    'html' =>true,
    'boln' =>' ',
    'eoln' =>'<br>'));

$data['BUTTON_BACK'] = '<a href="' . xarModURL('commerce','user',(FILENAME_ADDRESS_BOOK, '', 'SSL') . '">' .
xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_back.gif'),
        'alt' => IMAGE_BUTTON_BACK);
. '</a>';
$data['BUTTON_DELETE'] = '<a href="' . xarModURL('commerce','user',(FILENAME_ADDRESS_BOOK_PROCESS, 'delete=' . $_GET['delete'] . '&action=deleteconfirm', 'SSL') . '">' .
xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_delete.gif'),
        'alt' => IMAGE_BUTTON_DELETE);
. '</a>';
  } else {

 include(DIR_WS_MODULES . 'address_book_details.php');

    if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $data['BUTTON_BACK'] = '<a href="' . xarModURL('commerce','user',(FILENAME_ADDRESS_BOOK, '', 'SSL') . '">' .
xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_back.gif'),
        'alt' => IMAGE_BUTTON_BACK);
    . '</a>';
    $data['BUTTON_UPDATE'] =
    xtc_draw_hidden_field('action', 'update') .
    xtc_draw_hidden_field('edit', $_GET['edit']) .
<input type="image" src="#xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_update.gif')#" border="0" alt=IMAGE_BUTTON_UPDATE>

    } else {
      if (sizeof($_SESSION['navigation']->snapshot) > 0) {
        $back_link = xarModURL('commerce','user',($_SESSION['navigation']->snapshot['page'], xtc_array_to_string($_SESSION['navigation']->snapshot['get'], array(xtc_session_name())), $_SESSION['navigation']->snapshot['mode']);
      } else {
        $back_link = xarModURL('commerce','user',(FILENAME_ADDRESS_BOOK, '', 'SSL');
      }
      $data['BUTTON_BACK'] = '<a href="' . $back_link . '">' .       xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_back.gif'),
        'alt' => IMAGE_BUTTON_BACK);
. '</a>';
      $data['BUTTON_UPDATE'] =
      xtc_draw_hidden_field('action', 'process') .
      <input type="image" src="#xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_continue.gif')#" border="0" alt=IMAGE_BUTTON_CONTINUE>;

    }
  }

  $data['language', $_SESSION['language'];
  $smarty->caching = 0;
  return data;
  ?>
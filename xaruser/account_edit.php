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

  if (!isset($_SESSION['customer_id'])) {

    xarRedirectResponse(xarModURL('commerce','user','login', '', 'SSL'));
  }


  if (isset($_POST['action']) && ($_POST['action'] == 'process')) {
    if (ACCOUNT_GENDER == 'true') $gender = xtc_db_prepare_input($_POST['gender']);
    $firstname = xtc_db_prepare_input($_POST['firstname']);
    $lastname = xtc_db_prepare_input($_POST['lastname']);
    if (ACCOUNT_DOB == 'true') $dob = xtc_db_prepare_input($_POST['dob']);
    $email_address = xtc_db_prepare_input($_POST['email_address']);
    $telephone = xtc_db_prepare_input($_POST['telephone']);
    $fax = xtc_db_prepare_input($_POST['fax']);

    $error = false;

    if (ACCOUNT_GENDER == 'true') {
      if ( ($gender != 'm') && ($gender != 'f') ) {
        $error = true;

        $messageStack->add('account_edit', ENTRY_GENDER_ERROR);
      }
    }

    if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
      $error = true;

      $messageStack->add('account_edit', ENTRY_FIRST_NAME_ERROR);
    }

    if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
      $error = true;

      $messageStack->add('account_edit', ENTRY_LAST_NAME_ERROR);
    }

    if (ACCOUNT_DOB == 'true') {
      if (checkdate(substr(xtc_date_raw($dob), 4, 2), substr(xtc_date_raw($dob), 6, 2), substr(xtc_date_raw($dob), 0, 4)) == false) {
        $error = true;

        $messageStack->add('account_edit', ENTRY_DATE_OF_BIRTH_ERROR);
      }
    }

    if (strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
      $error = true;

      $messageStack->add('account_edit', ENTRY_EMAIL_ADDRESS_ERROR);
    }

    if (xarModAPIFunc('commerce','user','validate_email',array('email' =>$email_address)) == false) {
      $error = true;

      $messageStack->add('account_edit', ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
    }

    $check_email_query = new xenQuery("select count(*) as total from " . TABLE_CUSTOMERS . " where customers_email_address = '" . xtc_db_input($email_address) . "' and customers_id != '" . (int)$_SESSION['customer_id'] . "'");
      $q = new xenQuery();
      $q->run();
    $check_email = $q->output();
    if ($check_email['total'] > 0) {
      $error = true;

      $messageStack->add('account_edit', ENTRY_EMAIL_ADDRESS_ERROR_EXISTS);
    }

    if (strlen($telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {
      $error = true;

      $messageStack->add('account_edit', ENTRY_TELEPHONE_NUMBER_ERROR);
    }

    if ($error == false) {
      $q->addfield('customers_firstname',$firstname);
                              $q->addfield('customers_lastname',$lastname);
                              $q->addfield('customers_email_address',$email_address);
                              $q->addfield('customers_telephone',$telephone);
                              $q->addfield('customers_fax',$fax);

      if (ACCOUNT_GENDER == 'true') $q->addfield('customers_gender',$gender);
      if (ACCOUNT_DOB == 'true') $q->addfield('customers_dob',xtc_date_raw($dob));

      xtc_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', "customers_id = '" . (int)$_SESSION['customer_id'] . "'");

      new xenQuery("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_account_last_modified = now() where customers_info_id = '" . (int)$_SESSION['customer_id'] . "'");

// reset the session variables
      $customer_first_name = $firstname;

      $messageStack->add_session('account', SUCCESS_ACCOUNT_UPDATED, 'success');

      xarRedirectResponse(xarModURL('commerce','user',(FILENAME_ACCOUNT, '', 'SSL'));
    }
  } else {
    $account_query = new xenQuery("select customers_gender, customers_firstname, customers_lastname, customers_dob, customers_email_address, customers_telephone, customers_fax from " . TABLE_CUSTOMERS . " where customers_id = '" . (int)$_SESSION['customer_id'] . "'");
      $q = new xenQuery();
      $q->run();
    $account = $q->output();
  }

  $breadcrumb->add(NAVBAR_TITLE_1_ACCOUNT_EDIT, xarModURL('commerce','user',(FILENAME_ACCOUNT, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2_ACCOUNT_EDIT, xarModURL('commerce','user',(FILENAME_ACCOUNT_EDIT, '', 'SSL'));

require(DIR_WS_INCLUDES . 'header.php');

  if ($messageStack->size('account_edit') > 0) {
  $data['error'] = $messageStack->output('account_edit');

  }

  if (ACCOUNT_GENDER == 'true') {
  $data['gender'] = '1';
    $male = ($account['customers_gender'] == 'm') ? true : false;
    $female = !$male;
  $data['INPUT_MALE'] = xtc_draw_radio_field('gender', 'm',$male);
  $data['INPUT_FEMALE'] = xtc_draw_radio_field('gender', 'f',$female).(xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_GENDER_TEXT))) ? '<span class="inputRequirement">' . ENTRY_GENDER_TEXT . '</span>': '');


  }
    $data['INPUT_FIRSTNAME',xtc_draw_input_field('firstname',$account['customers_firstname']) . '&nbsp;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_FIRST_NAME_TEXT)) ? '<span class="inputRequirement">' . ENTRY_FIRST_NAME_TEXT . '</span>': '');
  $data['INPUT_LASTNAME'] = xtc_draw_input_field('lastname',$account['customers_lastname']) . '&nbsp;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_LAST_NAME_TEXT)) ? '<span class="inputRequirement">' . ENTRY_LAST_NAME_TEXT . '</span>': '');


  if (ACCOUNT_DOB == 'true') {
  $data['birthdate'] = '1';
  $data['INPUT_DOB'] = xtc_draw_input_field('dob',xarModAPIFunc('commerce','user','date_short',array('raw_date' =>$account['customers_dob']))
) . '&nbsp;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_DATE_OF_BIRTH_TEXT)) ? '<span class="inputRequirement">' . ENTRY_DATE_OF_BIRTH_TEXT . '</span>': '');

  }
  $data['INPUT_EMAIL'] = xtc_draw_input_field('email_address',$account['customers_email_address']) . '&nbsp;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_EMAIL_ADDRESS_TEXT)) ? '<span class="inputRequirement">' . ENTRY_EMAIL_ADDRESS_TEXT . '</span>': '');
  $data['INPUT_TEL'] = xtc_draw_input_field('telephone',$account['customers_telephone']) . '&nbsp;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_TELEPHONE_NUMBER_TEXT)) ? '<span class="inputRequirement">' . ENTRY_TELEPHONE_NUMBER_TEXT . '</span>': '');
  $data['INPUT_FAX'] = xtc_draw_input_field('fax',$account['customers_fax']) . '&nbsp;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_FAX_NUMBER_TEXT)) ? '<span class="inputRequirement">' . ENTRY_FAX_NUMBER_TEXT . '</span>': '');
  $data['BUTTON_BACK'] = '<a href="' . xarModURL('commerce','user',(FILENAME_ACCOUNT, '', 'SSL') . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_back.gif'),'alt' => IMAGE_BUTTON_BACK); . '</a>';
  $data['BUTTON_SUBMIT'] = <input type="image" src="#xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_continue.gif')#" border="0" alt=IMAGE_BUTTON_CONTINUE>;

  $data['language'] = $_SESSION['language'];

  $smarty->caching = 0;
  return data;
  ?>
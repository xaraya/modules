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
  require_once(DIR_FS_INC . 'xtc_get_country_list.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_countries.inc.php');
  require_once(DIR_FS_INC . 'xtc_draw_checkbox_field.inc.php');
  require_once(DIR_FS_INC . 'xtc_draw_password_field.inc.php');
  require_once(DIR_FS_INC . 'xtc_encrypt_password.inc.php');
  require_once(DIR_FS_INC . 'xtc_php_mail.inc.php');

  require_once(DIR_WS_CLASSES . 'class.phpmailer.php');



  $process = false;
  if (isset($_POST['action']) && ($_POST['action'] == 'process')) {
    $process = true;

    if (ACCOUNT_GENDER == 'true') $gender = xtc_db_prepare_input($_POST['gender']);
    $firstname = xtc_db_prepare_input($_POST['firstname']);
    $lastname = xtc_db_prepare_input($_POST['lastname']);
    if (ACCOUNT_DOB == 'true') $dob = xtc_db_prepare_input($_POST['dob']);
    $email_address = xtc_db_prepare_input($_POST['email_address']);
    if (ACCOUNT_COMPANY == 'true') $company = xtc_db_prepare_input($_POST['company']);
    $street_address = xtc_db_prepare_input($_POST['street_address']);
    if (ACCOUNT_SUBURB == 'true') $suburb = xtc_db_prepare_input($_POST['suburb']);
    $postcode = xtc_db_prepare_input($_POST['postcode']);
    $city = xtc_db_prepare_input($_POST['city']);
    $zone_id = xtc_db_prepare_input($_POST['zone_id']);
    if (ACCOUNT_STATE == 'true') $state = xtc_db_prepare_input($_POST['state']);
    $country = xtc_db_prepare_input($_POST['country']);
    $telephone = xtc_db_prepare_input($_POST['telephone']);
    $fax = xtc_db_prepare_input($_POST['fax']);
    $newsletter = xtc_db_prepare_input($_POST['newsletter']);
    $password = xtc_db_prepare_input($_POST['password']);
    $confirmation = xtc_db_prepare_input($_POST['confirmation']);

    $error = false;

    if (ACCOUNT_GENDER == 'true') {
      if ( ($gender != 'm') && ($gender != 'f') ) {
        $error = true;

        $messageStack->add('create_account', ENTRY_GENDER_ERROR);
      }
    }

    if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
      $error = true;

      $messageStack->add('create_account', ENTRY_FIRST_NAME_ERROR);
    }

    if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
      $error = true;

      $messageStack->add('create_account', ENTRY_LAST_NAME_ERROR);
    }

    if (ACCOUNT_DOB == 'true') {
      if (checkdate(substr(xtc_date_raw($dob), 4, 2), substr(xtc_date_raw($dob), 6, 2), substr(xtc_date_raw($dob), 0, 4)) == false) {
        $error = true;

        $messageStack->add('create_account', ENTRY_DATE_OF_BIRTH_ERROR);
      }
    }

    if (strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
      $error = true;

      $messageStack->add('create_account', ENTRY_EMAIL_ADDRESS_ERROR);
    } elseif (xarModAPIFunc('commerce','user','validate_email',array('email' => $email_address)) == false) {
      $error = true;

      $messageStack->add('create_account', ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
    } else {
      $check_email_query = new xenQuery("select count(*) as total from " . TABLE_CUSTOMERS . " where customers_email_address = '" . xtc_db_input($email_address) . "'");
      $q = new xenQuery();
      $q->run();
      $check_email = $q->output();
      if ($check_email['total'] > 0) {
        $error = true;

        $messageStack->add('create_account', ENTRY_EMAIL_ADDRESS_ERROR_EXISTS);
      }
    }

    if (strlen($street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
      $error = true;

      $messageStack->add('create_account', ENTRY_STREET_ADDRESS_ERROR);
    }

    if (strlen($postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
      $error = true;

      $messageStack->add('create_account', ENTRY_POST_CODE_ERROR);
    }

    if (strlen($city) < ENTRY_CITY_MIN_LENGTH) {
      $error = true;

      $messageStack->add('create_account', ENTRY_CITY_ERROR);
    }

    if (is_numeric($country) == false) {
      $error = true;

      $messageStack->add('create_account', ENTRY_COUNTRY_ERROR);
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
        if ($zone_query->getrows() > 1) {
        $zone_query = new xenQuery("select distinct zone_id from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country . "' and zone_name = '" . xtc_db_input($state) . "'");
        }
        if ($zone_query->getrows() >= 1) {
      $q = new xenQuery();
      $q->run();
          $zone = $q->output();
          $zone_id = $zone['zone_id'];
        } else {
          $error = true;

          $messageStack->add('create_account', ENTRY_STATE_ERROR_SELECT);
        }
      } else {
        if (strlen($state) < ENTRY_STATE_MIN_LENGTH) {
          $error = true;

          $messageStack->add('create_account', ENTRY_STATE_ERROR);
        }
      }
    }

    if (strlen($telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {
      $error = true;

      $messageStack->add('create_account', ENTRY_TELEPHONE_NUMBER_ERROR);
    }


    if (strlen($password) < ENTRY_PASSWORD_MIN_LENGTH) {
      $error = true;

      $messageStack->add('create_account', ENTRY_PASSWORD_ERROR);
    } elseif ($password != $confirmation) {
      $error = true;

      $messageStack->add('create_account', ENTRY_PASSWORD_ERROR_NOT_MATCHING);
    }

    if ($error == false) {
      $q->addfield('customers_status',DEFAULT_CUSTOMERS_STATUS_ID);
                              $q->addfield('customers_firstname',$firstname);
                              $q->addfield('customers_lastname',$lastname);
                              $q->addfield('customers_email_address',$email_address);
                              $q->addfield('customers_telephone',$telephone);
                              $q->addfield('customers_fax',$fax);
                              $q->addfield('customers_newsletter',$newsletter);
                              $q->addfield('customers_password',xtc_encrypt_password($password));

      if (ACCOUNT_GENDER == 'true') $q->addfield('customers_gender',$gender);
      if (ACCOUNT_DOB == 'true') $q->addfield('customers_dob',xtc_date_raw($dob));

      xtc_db_perform(TABLE_CUSTOMERS, $sql_data_array);

      $_SESSION['customer_id'] = xtc_db_insert_id();

      $q->addfield('customers_id',$_SESSION['customer_id']);
                              $q->addfield('entry_firstname',$firstname);
                              $q->addfield('entry_lastname',$lastname);
                              $q->addfield('entry_street_address',$street_address);
                              $q->addfield('entry_postcode',$postcode);
                              $q->addfield('entry_city',$city);
                              $q->addfield('entry_country_id',$country);

      if (ACCOUNT_GENDER == 'true') $q->addfield('entry_gender',$gender);
      if (ACCOUNT_COMPANY == 'true') $q->addfield('entry_company',$company);
      if (ACCOUNT_SUBURB == 'true') $q->addfield('entry_suburb',$suburb);
      if (ACCOUNT_STATE == 'true') {
        if ($zone_id > 0) {
          $q->addfield('entry_zone_id',$zone_id);
          $q->addfield('entry_state','');
        } else {
          $q->addfield('entry_zone_id','0');
          $q->addfield('entry_state',$state);
        }
      }

      xtc_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);

      $address_id = xtc_db_insert_id();

      new xenQuery("update " . TABLE_CUSTOMERS . " set customers_default_address_id = '" . $address_id . "' where customers_id = '" . (int)$_SESSION['customer_id'] . "'");

      new xenQuery("insert into " . TABLE_CUSTOMERS_INFO . " (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created) values ('" . (int)$_SESSION['customer_id'] . "', '0', now())");

      if (SESSION_RECREATE == 'True') {
        xtc_session_recreate();
      }

      $_SESSION['customer_first_name'] = $firstname;
      $_SESSION['customer_default_address_id'] = $address_id;
      $_SESSION['customer_country_id'] = $country;
      $_SESSION['customer_zone_id'] = $zone_id;

      // restore cart contents
      $_SESSION['cart']->restore_contents();

      // build the message content
      $name = $firstname . ' ' . $lastname;


      // load data into array
      $module_content = array();
      $module_content = array(
        'MAIL_NAME' => $name,
        'MAIL_REPLY_ADDRESS' => EMAIL_SUPPORT_REPLY_ADDRESS,
        'MAIL_GENDER'=>$gender);

      // assign data to smarty
      $data['language'] =  $_SESSION['language'];
      $data['content'] =  $module_content;
      $smarty->caching = false;

      // create templates
      $smarty->caching = 0;
      $html_mail = $smarty->fetch(CURRENT_TEMPLATE . '/mail/create_account_mail.html');
      $smarty->caching = 0;
      $txt_mail = $smarty->fetch(CURRENT_TEMPLATE . '/mail/create_account_mail.txt');

      xtc_php_mail(EMAIL_SUPPORT_ADDRESS,EMAIL_SUPPORT_NAME,$email_address , $name , EMAIL_SUPPORT_FORWARDING_STRING, EMAIL_SUPPORT_REPLY_ADDRESS, EMAIL_SUPPORT_REPLY_ADDRESS_NAME, '', '', EMAIL_SUPPORT_SUBJECT, $html_mail, $txt_mail);

      if (!isset($mail_error)) {
          xarRedirectResponse(xarModURL('commerce','user','shopping_cart', '', 'SSL'));
      }
      else {
          echo $mail_error;
      }
    }
  }

  $breadcrumb->add(NAVBAR_TITLE_CREATE_ACCOUNT, xarModURL('commerce','user','create_account', '', 'SSL'));

 require(DIR_WS_INCLUDES . 'header.php');


  if ($messageStack->size('create_account') > 0) {
  $data['error'] = $messageStack->output('create_account');

  }
  if (ACCOUNT_GENDER == 'true') {
  $data['gender'] = '1';

  $data['INPUT_MALE'] = xtc_draw_radio_field('gender', 'm');
  $data['INPUT_FEMALE'] = xtc_draw_radio_field('gender', 'f').(xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_GENDER_TEXT)) ? '<span class="inputRequirement">' . ENTRY_GENDER_TEXT . '</span>': '');

  } else {
    $data['gender'] = '0';
    }

  $data['INPUT_FIRSTNAME'] = xtc_draw_input_field('firstname') . '&nbsp;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_FIRST_NAME_TEXT)) ? '<span class="inputRequirement">' . ENTRY_FIRST_NAME_TEXT . '</span>': '');
  $data['INPUT_LASTNAME'] = xtc_draw_input_field('lastname') . '&nbsp;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_LAST_NAME_TEXT)) ? '<span class="inputRequirement">' . ENTRY_LAST_NAME_TEXT . '</span>': '');

  if (ACCOUNT_DOB == 'true') {
  $data['birthdate'] = '1';

  $data['INPUT_DOB'] = xtc_draw_input_field('dob') . '&nbsp;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_DATE_OF_BIRTH_TEXT)) ? '<span class="inputRequirement">' . ENTRY_DATE_OF_BIRTH_TEXT . '</span>': '');

  }  else {
  $data['birthdate'] = '0';
  }

  $data['INPUT_EMAIL'] = xtc_draw_input_field('email_address') . '&nbsp;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_EMAIL_ADDRESS_TEXT)) ? '<span class="inputRequirement">' . ENTRY_EMAIL_ADDRESS_TEXT . '</span>': '');

  if (ACCOUNT_COMPANY == 'true') {
  $data['company'] = '1';
  $data['INPUT_COMPANY'] = xtc_draw_input_field('company') . '&nbsp;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_COMPANY_TEXT)) ? '<span class="inputRequirement">' . ENTRY_COMPANY_TEXT . '</span>': '');
  }  else {
  $data['company'] = '0';
  }

  $data['INPUT_STREET'] = xtc_draw_input_field('street_address') . '&nbsp;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_STREET_ADDRESS_TEXT)) ? '<span class="inputRequirement">' . ENTRY_STREET_ADDRESS_TEXT . '</span>': '');

  if (ACCOUNT_SUBURB == 'true') {
  $data['suburb'] = '1';
 $data['INPUT_SUBURB'] = xtc_draw_input_field('suburb') . '&nbsp;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_SUBURB_TEXT)) ? '<span class="inputRequirement">' . ENTRY_SUBURB_TEXT . '</span>': '');

  } else {
  $data['suburb'] = '0';
  }

  $data['INPUT_CODE'] = xtc_draw_input_field('postcode') . '&nbsp;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_POST_CODE_TEXT)) ? '<span class="inputRequirement">' . ENTRY_POST_CODE_TEXT . '</span>': '');
  $data['INPUT_CITY'] = xtc_draw_input_field('city') . '&nbsp;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_CITY_TEXT)) ? '<span class="inputRequirement">' . ENTRY_CITY_TEXT . '</span>': '');

  if (ACCOUNT_STATE == 'true') {
  $data['state'] = '1';

    if ($process == true) {
      if ($entry_state_has_zones == true) {
        $zones_array = array();
        $zones_query = new xenQuery("select zone_name from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country . "' order by zone_name");
        while ($zones_values = $q->output()($zones_query)) {
          $zones_array[] = array('id' => $zones_values['zone_name'], 'text' => $zones_values['zone_name']);
        }
        $state_input= commerce_userapi_draw_pull_down_menu('state', $zones_array);
      } else {
        $state_input= xtc_draw_input_field('state');
      }
    } else {
      $state_input= xtc_draw_input_field('state');
    }

    if (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_STATE_TEXT))) $state_input.= '&nbsp;<span class="inputRequirement">' . ENTRY_STATE_TEXT;

   $data['INPUT_STATE'] = $state_input;
  } else {
  $data['state'] = '0';
  }

  $data['SELECT_COUNTRY'] = xtc_get_country_list('country', STORE_COUNTRY) . '&nbsp;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_COUNTRY_TEXT)) ? '<span class="inputRequirement">' . ENTRY_COUNTRY_TEXT . '</span>': '');
  $data['INPUT_TEL'] = xtc_draw_input_field('telephone') . '&nbsp;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_TELEPHONE_NUMBER_TEXT)) ? '<span class="inputRequirement">' . ENTRY_TELEPHONE_NUMBER_TEXT . '</span>': '');
  $data['INPUT_FAX'] = xtc_draw_input_field('fax') . '&nbsp;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_FAX_NUMBER_TEXT)) ? '<span class="inputRequirement">' . ENTRY_FAX_NUMBER_TEXT . '</span>': '');
  $data['CHECKBOX_NEWSLETTER'] = xtc_draw_checkbox_field('newsletter', '1') . '&nbsp;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_NEWSLETTER_TEXT)) ? '<span class="inputRequirement">' . ENTRY_NEWSLETTER_TEXT . '</span>': '');
  $data['INPUT_PASSWORD'] = xtc_draw_password_field('password') . '&nbsp;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_PASSWORD_TEXT)) ? '<span class="inputRequirement">' . ENTRY_PASSWORD_TEXT . '</span>': '');
  $data['INPUT_CONFIRMATION'] = xtc_draw_password_field('confirmation') . '&nbsp;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_PASSWORD_CONFIRMATION_TEXT)) ? '<span class="inputRequirement">' . ENTRY_PASSWORD_CONFIRMATION_TEXT . '</span>': '');

  $data['language'] = $_SESSION['language'];
  $smarty->caching = 0;
  $data['BUTTON_SUBMIT'] = <input type="image" src="#xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_continue.gif')#" border="0" alt=IMAGE_BUTTON_CONTINUE>;
  return data;
  ?>
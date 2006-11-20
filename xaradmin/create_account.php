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

function commerce_admin_create_account()
{
    sys::import('modules.xen.xarclasses.xenquery');
    include_once 'modules/commerce/xarclasses/object_info.php';
    $xartables = xarDBGetTables();
    $configuration = xarModAPIFunc('commerce','admin','load_configuration');
    $data['configuration'] = $configuration;

    $customers_statuses_array = xarModAPIFunc('commerce','user','get_customers_statuses');

    if(!xarVarFetch('customers_firstname','str',  $customers_firstname, '', XARVAR_NOT_REQUIRED)) {return;}
    $data['customers_firstname'] = $customers_firstname;
    if(!xarVarFetch('customers_lastname','str',  $customers_lastname, '', XARVAR_NOT_REQUIRED)) {return;}
    $data['customers_lastname'] = $customers_lastname;
    if(!xarVarFetch('customers_email_address','str',  $customers_email_address, '', XARVAR_NOT_REQUIRED)) {return;}
    $data['customers_email_address'] = $customers_email_address;
    if(!xarVarFetch('customers_telephone','str',  $customers_telephone, '', XARVAR_NOT_REQUIRED)) {return;}
    $data['customers_telephone'] = $customers_telephone;
    if(!xarVarFetch('customers_fax','str',  $customers_fax, '', XARVAR_NOT_REQUIRED)) {return;}
    $data['customers_fax'] = $customers_fax;
    if(!xarVarFetch('customers_status','int',  $customers_status, 0, XARVAR_NOT_REQUIRED)) {return;}
    $data['customers_status'] = $customers_status;
    if(!xarVarFetch('customers_gender','str',  $customers_gender, '', XARVAR_NOT_REQUIRED)) {return;}
    $data['customers_gender'] = $customers_gender;
    if(!xarVarFetch('customers_dob','str',  $customers_dob, '', XARVAR_NOT_REQUIRED)) {return;}
    $data['customers_dob'] = $customers_dob;
    if(!xarVarFetch('default_address_id','int',  $default_address_id, '', XARVAR_NOT_REQUIRED)) {return;}
    $data['default_address_id'] = $default_address_id;
    if(!xarVarFetch('entry_street_address','str',  $entry_street_address, '', XARVAR_NOT_REQUIRED)) {return;}
    $data['entry_street_address'] = $entry_street_address;
    if(!xarVarFetch('entry_suburb','str',  $entry_suburb, '', XARVAR_NOT_REQUIRED)) {return;}
    $data['entry_suburb'] = $entry_suburb;
    if(!xarVarFetch('entry_postcode','str',  $entry_postcode, '', XARVAR_NOT_REQUIRED)) {return;}
    $data['entry_postcode'] = $entry_postcode;
    if(!xarVarFetch('entry_city','str',  $entry_city, '', XARVAR_NOT_REQUIRED)) {return;}
    $data['entry_city'] = $entry_city;
    if(!xarVarFetch('entry_country_id','int',  $entry_country_id, $configuration['store_country'], XARVAR_NOT_REQUIRED)) {return;}
    $data['entry_country_id'] = $entry_country_id;
    if(!xarVarFetch('entry_company','str',  $entry_company, '', XARVAR_NOT_REQUIRED)) {return;}
    $data['entry_company'] = $entry_company;
    if(!xarVarFetch('entry_state','str',  $entry_state, '', XARVAR_NOT_REQUIRED)) {return;}
    $data['entry_state'] = $entry_state;
    if(!xarVarFetch('entry_zone_id','int',  $entry_zone_id, 0, XARVAR_NOT_REQUIRED)) {return;}
    $data['entry_zone_id'] = $entry_zone_id;
    if(!xarVarFetch('customers_send_mail','str',  $customers_send_mail, '', XARVAR_NOT_REQUIRED)) {return;}
    $data['customers_send_mail'] = $customers_send_mail;
    if(!xarVarFetch('customers_password','str',  $customers_password, '', XARVAR_NOT_REQUIRED)) {return;}
    if ($customers_password == '') $customers_password = xarModAPIFunc('commerce','admin','create_password',array('length'=>8));
    $data['customers_password'] = $customers_password;
    if(!xarVarFetch('customers_mail_comments','str',  $customers_mail_comments, '', XARVAR_NOT_REQUIRED)) {return;}
    $data['customers_mail_comments'] = $customers_mail_comments;

    $data['error'] = false; // reset error flag

    if ($configuration['account_gender'] == 'true') {
        if ( ($customers_gender != 'm') && ($customers_gender != 'f') ) {
            $data['error'] = true;
            $data['entry_gender_error'] = true;
        } else {
            $data['entry_gender_error'] = false;
        }
    }

    if (strlen($customers_password) < $configuration['entry_password_min_length']) {
      $data['error'] = true;
      $data['entry_password_error'] = true;
    } else {
      $data['entry_password_error'] = false;
    }

    if ( ($customers_send_mail != 'yes') && ($customers_send_mail != 'no')) {
      $data['error'] = true;
      $data['entry_mail_error'] = true;
    } else {
      $data['entry_mail_error'] = false;
    }

    if (strlen($customers_firstname) < $configuration['entry_first_name_min_length']) {
        $data['error'] = true;
        $data['entry_firstname_error'] = true;
    } else {
        $data['entry_firstname_error'] = false;
    }

     if (strlen($customers_lastname) < $configuration['entry_last_name_min_length']) {
        $data['error'] = true;
        $data['entry_lastname_error'] = true;
    } else {
        $data['entry_lastname_error'] = false;
    }

    if ($configuration['account_dob'] == 'true') {
        if (checkdate(substr(xarModAPIFunc('commerce','admin','date_raw',array('date'=>$customers_dob)), 4, 2), substr(xarModAPIFunc('commerce','admin','date_raw',array('date'=>$customers_dob)), 6, 2), substr(xarModAPIFunc('commerce','admin','date_raw',array('date'=>$customers_dob)), 0, 4))) {
            $data['entry_date_of_birth_error'] = false;
        } else {
            $data['error'] = true;
            $data['entry_date_of_birth_error'] = true;
        }
    }

    if (strlen($customers_email_address) < $configuration['entry_email_address_min_length']) {
        $data['error'] = true;
        $data['entry_email_address_error'] = true;
    } else {
        $data['entry_email_address_error'] = false;
    }

    if (!xarModAPIFunc('commerce','user','validate_email',array('email' =>$customers_email_address))) {
        $data['error'] = true;
        $data['entry_email_address_check_error'] = true;
    } else {
        $data['entry_email_address_check_error'] = false;
    }
    $q = new xenQuery('SELECT',$xartables['commerce_customers']);
    $q->addfield('customers_email_address');
    $q->eq('customers_email_address',$customers_email_address);
    if(!$q->run()) return;
    if ($q->getrows()) {
        $data['error'] = true;
        $data['entry_email_address_exists'] = true;
    } else {
        $data['entry_email_address_exists'] = false;
    }

    if (strlen($entry_street_address) < $configuration['entry_street_address_min_length']) {
        $data['error'] = true;
        $data['entry_street_address_error'] = true;
    } else {
        $data['entry_street_address_error'] = false;
    }

    if (strlen($entry_postcode) < $configuration['entry_postcode_min_length']) {
        $data['error'] = true;
        $data['entry_post_code_error'] = true;
    } else {
        $data['entry_post_code_error'] = false;
    }

    if (strlen($entry_city) < $configuration['entry_city_min_length']) {
        $data['error'] = true;
        $data['entry_city_error'] = true;
    } else {
        $data['entry_city_error'] = false;
    }

    if ($entry_country_id == false) {
        $data['error'] = true;
        $data['entry_country_error'] = true;
    } else {
        $data['entry_country_error'] = false;
    }

    if ($configuration['account_state'] == 'true') {
        if ($data['entry_country_error'] == true) {
            $data['entry_state_error'] = true;
        } else {
            $zone_id = 0;
            $data['entry_state_error'] = false;
            $q = new xenQuery('SELECT',$xartables['commerce_zones'],array('zone_id AS id','zone_name AS text'));
            $q->eq('zone_country_id',$entry_country_id);
            if(!$q->run()) return;
            if ($q->getrows() > 0) {
                $data['zones'] = $q->output();
            } else {
                $data['zones'] = '';
                if ($entry_state == '') {
                    $data['error'] = true;
                    $data['entry_state_error'] = true;
                }
            }
        }
    }

    if (strlen($customers_telephone) < $configuration['entry_telephone_min_length']) {
        $data['error'] = true;
        $data['entry_telephone_error'] = true;
    } else {
        $data['entry_telephone_error'] = false;
    }

    if(!xarVarFetch('action', 'str',  $action, NULL, XARVAR_DONT_SET)) {return;}
    if (isset($action)) {
        switch ($action) {
            case 'edit':
                if ($data['error'] == false) {
                    $q = new xenQuery('INSERT',$xartables['commerce_customers']);
                    $q->addfield('customers_status',$customers_status);
                    $q->addfield('customers_firstname',$customers_firstname);
                    $q->addfield('customers_lastname',$customers_lastname);
                    $q->addfield('customers_email_address',$customers_email_address);
                    $q->addfield('customers_telephone',$customers_telephone);
                    $q->addfield('customers_fax',$customers_fax);
//                    $q->addfield('customers_newsletter',$customers_newsletter);
                    $q->addfield('customers_password',md5($customers_password));

                    if ($configuration['account_gender'] == 'true') $q->addfield('customers_gender',$customers_gender);
                    if ($configuration['account_dob'] == 'true') $q->addfield('customers_dob',xarModAPIFunc('commerce','admin','date_raw',array('date'=>$customers_dob)));
                    if(!$q->run()) return;

                    $dbconn = $q->getconnection();
                    $cc_id = $dbconn->PO_Insert_ID($xartables['commerce_customers'],'customers_id');

                    $q = new xenQuery('INSERT',$xartables['commerce_address_book']);
                    $q->addfield('entry_firstname',$customers_firstname);
                    $q->addfield('entry_lastname',$customers_lastname);
                    $q->addfield('entry_street_address',$entry_street_address);
                    $q->addfield('entry_postcode',$entry_postcode);
                    $q->addfield('entry_city',$entry_city);
                    $q->addfield('entry_country_id',$entry_country_id);

                    if ($configuration['account_gender'] == 'true') $q->addfield('entry_gender',$customers_gender);
                    if ($configuration['account_company'] == 'true') $q->addfield('entry_company',$entry_company);
                    if ($configuration['account_suburb'] == 'true') $q->addfield('entry_suburb',$entry_suburb);

                    if ($configuration['account_state'] == 'true') {
                        if ($entry_zone_id > 0) {
                            $q->addfield('entry_zone_id',$entry_zone_id);
                            $q->addfield('entry_state','');
                        } else {
                            $q->addfield('entry_zone_id','0');
                            $q->addfield('entry_state',$entry_state);
                        }
                    }

                    $q->addfield('customers_id',$cc_id);
                    if(!$q->run()) return;

                    $address_id = $dbconn->PO_Insert_ID($xartables['commerce_customers'],'customers_id');
                    $q = new xenQuery('UPDATE',$xartables['commerce_customers']);
                    $q->addfield('customers_default_address_id',$address_id);
                    $q->eq('customers_id',$cc_id);
                    if(!$q->run()) return;

                    $q = new xenQuery('INSERT',$xartables['commerce_customers_info']);
                    $q->addfield('customers_info_id',$cc_id);
                    $q->addfield('customers_info_number_of_logons',0);
                    $q->addfield('customers_info_date_account_created',time());
                    if(!$q->run()) return;

                    // Create insert into admin access table if admin is created.
                    if ($customers_status=='0') {
                        $q = new xenQuery("INSERT",$xartables['commerce_admin_access']);
                        $q->addfield('customers_id',$cc_id);
                        $q->addfield('start',1);
                        if(!$q->run()) return;
                    }
                    // Create eMail
                    if ( ($customers_send_mail != 'yes')) {
                        $data['tpl_path'] = 'modules/commerce/xartemplates/';

                        $data['name'] = $customers_lastname . ' ' . $customers_firstname;
                        $data['email'] = $customers_email_address;
                        $data['comments'] = $customers_mail_comments;
                        $data['password'] = $customers_password;

//                    $html_mail=$smarty->fetch(CURRENT_TEMPLATE . '/admin/create_account_mail.html');
//                    $txt_mail=$smarty->fetch(CURRENT_TEMPLATE . '/admin/create_account_mail.txt');


//                    xtc_php_mail(EMAIL_SUPPORT_ADDRESS,EMAIL_SUPPORT_NAME,$customers_email_address , $customers_lastname . ' ' . $customers_firstname , EMAIL_SUPPORT_FORWARDING_STRING, EMAIL_SUPPORT_REPLY_ADDRESS, EMAIL_SUPPORT_REPLY_ADDRESS_NAME, '', '', EMAIL_SUPPORT_SUBJECT, $html_mail , $txt_mail);
                    }

                    xarResponseRedirect(xarModURL('commerce','admin','customers',array('cID' => $cc_id)));
                }
        }
    }
    $localeinfo = xarLocaleGetInfo(xarMLSGetSiteLocale());
    $data['language'] = $localeinfo['lang'] . "_" . $localeinfo['country'];
    $data['customers_statuses_array'] = $customers_statuses_array;
    return $data;
}
?>
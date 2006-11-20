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

function commerce_admin_edit_account()
{
    sys::import('modules.xen.xarclasses.xenquery');
    include_once 'modules/commerce/xarclasses/object_info.php';
    $xartables = xarDBGetTables();
    $configuration = xarModAPIFunc('commerce','admin','load_configuration');
    $data['configuration'] = $configuration;

    if(!xarVarFetch('action', 'str',  $action, NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('cID',    'int',  $cID, 0, XARVAR_DONT_SET)) {return;}
    $data['cID'] = $cID;

    if ($action == 'load') {
        $q = new xenQuery('SELECT');
        $q->addtable($xartables['commerce_customers'],'c');
        $q->addtable($xartables['commerce_address_book'],'a');

        $q->addfields(array('c.customers_gender','c.customers_status', 'c.member_flag', 'c.customers_firstname', 'c.customers_lastname', 'c.customers_dob', 'c.customers_email_address','c.customers_telephone', 'c.customers_fax', 'c.customers_newsletter', 'c.customers_default_address_id'));
        $q->addfields(array('a.entry_company', 'a.entry_street_address', 'a.entry_suburb', 'a.entry_postcode', 'a.entry_city', 'a.entry_state', 'a.entry_zone_id', 'a.entry_country_id'));

        $q->join('c.customers_id','a.customers_id');
        $q->join('c.customers_default_address_id','a.address_book_id');

        $q->eq('c.customers_id',$cID);
        if(!$q->run()) return;
        $customer = $q->row();

        $customers_firstname = $customer['customers_firstname'];
        $customers_lastname = $customer['customers_lastname'];
        $customers_email_address = $customer['customers_email_address'];
        $customers_telephone = $customer['customers_telephone'];
        $customers_fax = $customer['customers_fax'];
        $customers_status = $customer['customers_status'];
        $customers_gender = $customer['customers_gender'];
        $customers_dob = xarModAPIFunc('commerce','admin','date_short',array('raw_date' =>$customer['customers_dob']));
        $default_address_id = $customer['customers_default_address_id'];
        $entry_street_address = $customer['entry_street_address'];
        $entry_suburb = $customer['entry_suburb'];
        $entry_postcode = $customer['entry_postcode'];
        $entry_city = $customer['entry_city'];
        $entry_country_id = $customer['entry_country_id'];
        $entry_company = $customer['entry_company'];
        $entry_state = $customer['entry_state'];
        $entry_zone_id = $customer['entry_zone_id'];
        $customers_mail_comments = '';
    }
    else {
        if(!xarVarFetch('customers_firstname','str',  $customers_firstname, '', XARVAR_NOT_REQUIRED)) {return;}
        if(!xarVarFetch('customers_lastname','str',  $customers_lastname, '', XARVAR_NOT_REQUIRED)) {return;}
        if(!xarVarFetch('customers_email_address','str',  $customers_email_address, '', XARVAR_NOT_REQUIRED)) {return;}
        if(!xarVarFetch('customers_telephone','str',  $customers_telephone, '', XARVAR_NOT_REQUIRED)) {return;}
        if(!xarVarFetch('customers_fax','str',  $customers_fax, '', XARVAR_NOT_REQUIRED)) {return;}
        if(!xarVarFetch('customers_status','int',  $customers_status, 0, XARVAR_NOT_REQUIRED)) {return;}
        if(!xarVarFetch('customers_gender','str',  $customers_gender, '', XARVAR_NOT_REQUIRED)) {return;}
        if(!xarVarFetch('customers_dob','str',  $customers_dob, '', XARVAR_NOT_REQUIRED)) {return;}
        if(!xarVarFetch('default_address_id','int',  $default_address_id, '', XARVAR_NOT_REQUIRED)) {return;}
        if(!xarVarFetch('entry_street_address','str',  $entry_street_address, '', XARVAR_NOT_REQUIRED)) {return;}
        if(!xarVarFetch('entry_suburb','str',  $entry_suburb, '', XARVAR_NOT_REQUIRED)) {return;}
        if(!xarVarFetch('entry_postcode','str',  $entry_postcode, '', XARVAR_NOT_REQUIRED)) {return;}
        if(!xarVarFetch('entry_city','str',  $entry_city, '', XARVAR_NOT_REQUIRED)) {return;}
        if(!xarVarFetch('entry_country_id','int',  $entry_country_id, $configuration['store_country'], XARVAR_NOT_REQUIRED)) {return;}
        if(!xarVarFetch('entry_company','str',  $entry_company, '', XARVAR_NOT_REQUIRED)) {return;}
        if(!xarVarFetch('entry_state','str',  $entry_state, '', XARVAR_NOT_REQUIRED)) {return;}
        if(!xarVarFetch('entry_zone_id','int',  $entry_zone_id, 0, XARVAR_NOT_REQUIRED)) {return;}
        if(!xarVarFetch('customers_mail_comments','str',  $customers_mail_comments, '', XARVAR_NOT_REQUIRED)) {return;}
    }

    $data['customers_firstname'] = $customers_firstname;
    $data['customers_lastname'] = $customers_lastname;
    $data['customers_email_address'] = $customers_email_address;
    $data['customers_telephone'] = $customers_telephone;
    $data['customers_fax'] = $customers_fax;
    $data['customers_status'] = $customers_status;
    $data['customers_gender'] = $customers_gender;
    $data['customers_dob'] = $customers_dob;
    $data['default_address_id'] = $default_address_id;
    $data['entry_street_address'] = $entry_street_address;
    $data['entry_suburb'] = $entry_suburb;
    $data['entry_postcode'] = $entry_postcode;
    $data['entry_city'] = $entry_city;
    $data['entry_country_id'] = $entry_country_id;
    $data['entry_company'] = $entry_company;
    $data['entry_state'] = $entry_state;
    $data['entry_zone_id'] = $entry_zone_id;
    $data['customers_mail_comments'] = $customers_mail_comments;

    $customers_statuses_array = xarModAPIFunc('commerce','user','get_customers_statuses');

    $data['error'] = false; // reset error flag

    if ($configuration['account_gender'] == 'true') {
        if ( ($customers_gender != 'm') && ($customers_gender != 'f') ) {
            $data['error'] = true;
            $data['entry_gender_error'] = true;
        } else {
            $data['entry_gender_error'] = false;
        }
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
    $q->ne('customers_id',$cID);
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

    $q = new xenQuery("SELECT",$xartables['commerce_customers_memo']);
    $q->addfields(array('memo_id','memo_text','memo_title','memo_date'));
    $q->setorder('memo_date','DESC');
    $q->eq('customers_id',$cID);
    if(!$q->run()) return;
    $data['memos'] = $q->output();

    if(!xarVarFetch('action', 'str',  $action, NULL, XARVAR_DONT_SET)) {return;}
    if (isset($action)) {
        switch ($action) {
            case 'edit':
                if ($data['error'] == false) {

                    $q = new xenQuery('UPDATE',$xartables['commerce_customers']);
                    $q->addfield('customers_firstname',$customers_firstname);
                    $q->addfield('customers_lastname',$customers_lastname);
                    $q->addfield('customers_email_address',$customers_email_address);
                    $q->addfield('customers_telephone',$customers_telephone);
                    $q->addfield('customers_fax',$customers_fax);
//                    $q->addfield('customers_newsletter',$customers_newsletter);

                    if ($configuration['account_gender'] == 'true') $q->addfield('customers_gender',$customers_gender);
                    if ($configuration['account_dob'] == 'true') $q->addfield('customers_dob',xarModAPIFunc('commerce','admin','date_raw',array('date'=>$customers_dob)));

                    $q->eq('customers_id',$cID);
                    if(!$q->run()) return;

                    $q = new xenQuery('UPDATE',$xartables['commerce_customers_info']);
                    $q->addfield('customers_info_date_account_last_modified',time());
                    $q->eq('customers_info_id',$cID);
                    if(!$q->run()) return;

                    $q = new xenQuery('UPDATE',$xartables['commerce_address_book']);
                    $q->addfield('entry_firstname',$customers_firstname);
                    $q->addfield('entry_lastname',$customers_lastname);
                    $q->addfield('entry_street_address',$entry_street_address);
                    $q->addfield('entry_postcode',$entry_postcode);
                    $q->addfield('entry_city',$entry_city);
                    $q->addfield('entry_country_id',$entry_country_id);

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

                    $q->eq('customers_id',$cID);
                    $q->eq('address_book_id',$default_address_id);
                    if(!$q->run()) return;
                    xarResponseRedirect(xarModURL('commerce','admin','customers',array('cID' => $cID)));
                }
        }
    }
    $localeinfo = xarLocaleGetInfo(xarMLSGetSiteLocale());
    $data['language'] = $localeinfo['lang'] . "_" . $localeinfo['country'];
    $data['customers_statuses_array'] = $customers_statuses_array;
    return $data;
}
?>
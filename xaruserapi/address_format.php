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

function commerce_userapi_address_format($args) {
    extract($args);
    if(!isset($address_format_id)
        || !isset($address)
        || !isset($html)
        || !isset($boln)
        || !isset($eoln)) {
        $msg = xarML('Wrong arguments to commerce_userapi_address_format');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION,
                    'BAD_PARAM',
                     new SystemException($msg));
        return false;
    }
    $address_format_query = new xenQuery("select address_format as format from " . TABLE_ADDRESS_FORMAT . " where address_format_id = '" . $address_format_id . "'");
    $address_format_query->run();
    $address_format = $address_format_query->row();

    $company = addslashes($address['company']);
    $firstname = addslashes($address['firstname']);
    $lastname = addslashes($address['lastname']);
    $street = addslashes($address['street_address']);
    $suburb = addslashes($address['suburb']);
    $city = addslashes($address['city']);
    $state = addslashes($address['state']);
    $country_id = $address['country_id'];
    $zone_id = $address['zone_id'];
    $postcode = addslashes($address['postcode']);
    $zip = $postcode;
    $country = xarModAPIFunc('commerce','user','get_country_name',array('country_id' =>$country_id));
    $state = xarModAPIFunc('commerce','user','get_zone_code',array('country_id' =>$country_id,'zone_id' =>$zone_id,'default_zone' =>$state));

    if ($html) {
// HTML Mode
      $HR = '<hr>';
      $hr = '<hr>';
      if ( ($boln == '') && ($eoln == "\n") ) { // Values not specified, use rational defaults
        $CR = '<br>';
        $cr = '<br>';
        $eoln = $cr;
      } else { // Use values supplied
        $CR = $eoln . $boln;
        $cr = $CR;
      }
    } else {
// Text Mode
      $CR = $eoln;
      $cr = $CR;
      $HR = '----------------------------------------';
      $hr = '----------------------------------------';
    }

    $statecomma = '';
    $streets = $street;
    if ($suburb != '') $streets = $street . $cr . $suburb;
    if ($firstname == '') $firstname = addslashes($address['name']);
    if ($country == '') $country = addslashes($address['country']);
    if ($state != '') $statecomma = $state . ', ';

    $fmt = $address_format['format'];
    eval("\$address = \"$fmt\";");

    if ( (ACCOUNT_COMPANY == 'true') && (xarModAPIFunc('commerce','user','not_null',array('arg' => $company))) ) {
      $address = $company . $cr . $address;
    }

    $address = stripslashes($address);

    return $address;
  }
 ?>

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

function commerce_userapi_address_summary($customers_id, $address_id) {
    $customers_id = xtc_db_prepare_input($customers_id);
    $address_id = xtc_db_prepare_input($address_id);

    $address_query = new xenQuery("select ab.entry_street_address, ab.entry_suburb, ab.entry_postcode, ab.entry_city, ab.entry_state, ab.entry_country_id, ab.entry_zone_id, c.countries_name, c.address_format_id from " . TABLE_ADDRESS_BOOK . " ab, " . TABLE_COUNTRIES . " c where ab.address_book_id = '" . xtc_db_input($address_id) . "' and ab.customers_id = '" . xtc_db_input($customers_id) . "' and ab.entry_country_id = c.countries_id");
      $q = new xenQuery();
      $q->run();
    $address = $q->output();

    $street_address = $address['entry_street_address'];
    $suburb = $address['entry_suburb'];
    $postcode = $address['entry_postcode'];
    $city = $address['entry_city'];
    $state = xtc_xarModAPIFunc('commerce','user','get_zone_code',array('country_id' =>$address['entry_country_id'],'zone_id' =>$address['entry_zone_id'],'default_zone' =>$address['entry_state']));
    $country = $address['countries_name'];

    $address_format_query = new xenQuery("select address_summary from " . TABLE_ADDRESS_FORMAT . " where address_format_id = '" . $address['address_format_id'] . "'");
      $q = new xenQuery();
      $q->run();
    $address_format = $q->output();

//    eval("\$address = \"{$address_format['address_summary']}\";");
    $address_summary = $address_format['address_summary'];
    eval("\$address = \"$address_summary\";");

    return $address;
  }
 ?>
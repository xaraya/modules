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

   // include needed functions
   require_once(DIR_FS_INC . 'xtc_get_address_format_id.inc.php');
function commerce_userapi_address_label($args) {
    extract($args);
    if(!isset($address_format_id)) {
    $msg = xarML('Wrong arguments to commerce_userapi_address_format');
    xarExceptionSet(XAR_SYSTEM_EXCEPTION,
                'BAD_PARAM',
                 new SystemException($msg));
    return false;
    }
    if(!isset($address_id)) $address_id = 1;
    if(!isset($html)) $html = false;
    if(!isset($boln)) $boln = '';
    if(!isset($eoln)) $eoln = "\n";
    $address_query = new xenQuery("select entry_firstname as firstname, entry_lastname as lastname, entry_company as company, entry_street_address as street_address, entry_suburb as suburb, entry_city as city, entry_postcode as postcode, entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $customers_id . "' and address_book_id = '" . $address_id . "'");
    $address_query->run();
    $address = $address_query->row();

    $format_id = xtc_get_address_format_id($address['country_id']);

    return xarModAPIFunc('commerce','user','address_format',array(
    'address_format_id' =>$format_id,
    'address' =>$address,
    'html' =>$html,
    'boln' =>$boln,
    'eoln' =>$eoln));
}
?>

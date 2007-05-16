<?php

/**
 * This function is called internally by the core whenever the module is
 * loaded.  It adds in the information
 */
function customers_xartables()
{
    $xartable = array();
    $xartable['customers_address_book'] = xarDB::getPrefix() . '_customers_address_book';
    $xartable['customers_customers_ip'] = xarDB::getPrefix() . '_customers_customers_ip';
    $xartable['customers_customers_status'] = xarDB::getPrefix() . '_customers_customers_status';
    $xartable['customers_customers_status_history'] = xarDB::getPrefix() . '_customers_customers_status_history';
    return $xartable;
}

?>

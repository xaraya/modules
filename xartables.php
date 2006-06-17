<?php

/**
 * This function is called internally by the core whenever the module is
 * loaded.  It adds in the information
 */
function customers_xartables()
{
    $xartable = array();
    $xartable['customers_address_book'] = xarDBGetSiteTablePrefix() . '_customers_address_book';
    $xartable['customers_customers_ip'] = xarDBGetSiteTablePrefix() . '_customers_customers_ip';
    $xartable['customers_customers_status'] = xarDBGetSiteTablePrefix() . '_customers_customers_status';
    $xartable['customers_customers_status_history'] = xarDBGetSiteTablePrefix() . '_customers_customers_status_history';
    return $xartable;
}

?>

<?php

/**
 * This function is called internally by the core whenever the module is
 * loaded.  It adds in the information
 */
function vendors_xartables()
{
    // Initialise table array
    $xartable = array();

    $vendors = xarDBGetSiteTablePrefix() . '_vendors_vendors';

    // Set the table name
    $xartable['vendors_vendors'] = $vendors;

    // Return the table information
    return $xartable;
}

?>

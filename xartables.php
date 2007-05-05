<?php

/**
 * This function is called internally by the core whenever the module is
 * loaded. It adds in the information
 */
function members_xartables()
{
    // Initialise table array
    $xartable = array();

    $members = xarDB::getPrefix() . '_members_members';
    $addresses = xarDB::getPrefix() . '_members_addresses';

    // Set the table name
    $xartable['members_members'] = $members;
    $xartable['members_addresses'] = $addresses;

    // Return the table information
    return $xartable;
}

?>

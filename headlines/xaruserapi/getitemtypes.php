<?php

/**
 * utility function to retrieve the list of item types of this module (if any)
 *
 * @returns array
 * @return array containing the item types and their description
 */
function headlines_userapi_getitemtypes($args)
{
    $itemtypes = array();
/*
    // do not use this if you only handle one type of items in your module
    $itemtypes[1] = array('label' => xarVarPrepForDisplay(xarML('Headlines')),
                          'title' => xarVarPrepForDisplay(xarML('View Headlines')),
                          'url'   => xarModURL('headlines','user','main'));
*/
    return $itemtypes;
}
?>
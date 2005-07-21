<?php

/**
 * utility function to retrieve the list of item types of this module (if any)
 *
 * @returns array
 * @return array containing the item types and their description
 */
function shopping_userapi_getitemtypes($args)
{
    $itemtypes = array();

/*  // sample definition of item types for hook calls
    $itemtypes[1] = array('label' => xarVarPrepForDisplay(xarML('Shopping Items')),
                          'title' => xarVarPrepForDisplay(xarML('View Shopping Items')),
                          'url'   => xarModURL('shopping','user','showitems'));
    $itemtypes[2] = array('label' => xarVarPrepForDisplay(xarML('Shopping Recommendations')),
                          'title' => ''),
                          'url'   => '');
    $itemtypes[3] = array('label' => xarVarPrepForDisplay(xarML('Shopping Profiles')),
                          'title' => ''),
                          'url'   => '');
    $itemtypes[4] = array('label' => xarVarPrepForDisplay(xarML('Shopping Carts')),
                          'title' => ''),
                          'url'   => '');
    $itemtypes[5] = array('label' => xarVarPrepForDisplay(xarML('Shopping Orders')),
                          'title' => ''),
                          'url'   => '');
    $itemtypes[6] = array('label' => xarVarPrepForDisplay(xarML('Shopping Order Details')),
                          'title' => ''),
                          'url'   => '');
    ...
*/

    return $itemtypes;
}

?>

<?php

/**
 * Utility function to retrieve the list of item types of this module (if any)
 *
 * @returns array
 * @return array containing the item types and their description
 * @todo remove the need to do xarVarPrepForDisplay() here - it should be done at the point of display.
 */

function xarpages_userapi_getitemtypes($args)
{
    $itemtypes = array();

    // Get publication types
    $pagetypes = xarModAPIFunc('xarpages', 'user', 'gettypes');

    foreach ($pagetypes as $pagetype) {
        $itemtypes[$pagetype['ptid']] = array(
            'label' => xarVarPrepForDisplay($pagetype['desc']),
            'title' => xarVarPrepForDisplay(xarML('Display #(1)', $pagetype['desc'])),
            'url'   => xarModURL('xarpages', 'user', 'display', array('ptid' => $pagetype['ptid']))
        );
    }
    return $itemtypes;
}

?>
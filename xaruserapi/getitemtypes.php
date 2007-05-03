<?php

/**
 * Utility function to retrieve the list of item types of this module (if any)
 *
 * @returns array
 * @return array containing the item types and their description
 * @todo remove the need to do xarVarPrepForDisplay() here - it should be done at the point of display.
 */

function ievents_userapi_getitemtypes($args)
{
    $itemtypes = array();

    // Get item types
    $types = array(
        1 => array(
            'name' => 'events',
            'desc' => xarML('Events'),
            xarModURL('ievents', 'user', 'view')
        ),
        2 => array(
            'name' => 'calendars',
            'desc' => xarML('Calendars'),
            xarModURL('ievents', 'user', 'viewcal')
        ),
    );

    foreach ($types as $key => $type) {
        $itemtypes[$key] = array(
            'label' => xarVarPrepForDisplay($type['desc']),
            'title' => xarVarPrepForDisplay(xarML('Display #(1)', $type['name'])),
            'url'   => $type['url'],
        );
    }

    return $itemtypes;
}

?>
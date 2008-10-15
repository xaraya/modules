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

    $itemtype_events = xarModGetVar('ievents', 'itemtype_events');
    $itemtype_calendars = xarModGetVar('ievents', 'itemtype_calendars');

    // Get item types
    $types = array(
        $itemtype_events => array(
            'name' => 'events',
            'desc' => xarML('Events'),
            'url' => xarModURL('ievents', 'user', 'view')
        ),
        $itemtype_calendars => array(
            'name' => 'calendars',
            'desc' => xarML('Calendars'),
            'url' => xarModURL('ievents', 'user', 'viewcal')
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

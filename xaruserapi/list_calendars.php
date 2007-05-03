<?php

/**
 * Get lists of calendars for drop-down lists (dynamic data properties).
 * This returns only calendars the current user is allowed to edit events on.
 * The lowest privilege level is COMMENT, which allows a user to submit an event
 * but not to approve it.
 */

function ievents_userapi_list_calendars($args)
{
    // Get all calendars the user is allowed to submit to.
    $calendars = xarModAPifunc('ievents', 'user','getcalendars', array('event_priv' => 'COMMENT', 'status' => 'ACTIVE'));

    $return = array();

    foreach($calendars as $calendar) {
        $return[$calendar['cid']] = $calendar['short_name'];
    }

    return $return;
}

?>
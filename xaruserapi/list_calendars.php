<?php

/**
 * Get lists of calendars for drop-down lists (dynamic data properties).
 * This returns only calendars the current user is allowed to edit events on.
 * The lowest privilege level is COMMENT, which allows a user to submit an event
 * but not to approve it.
 * If the 'readable' flag is set, then privileges are lowered a little so that
 * all calendars the user can read are returned (not just editable).
 */

function ievents_userapi_list_calendars($args)
{
    extract($args);

    // If the user as an administrator, then allow INACTIVE calendars.
    // TODO: this check has cropped up in a number of places now. Can they be combined?
    if (xarSecurityCheck('AdminIEvent', 0, 'IEvent', 'All:All:All')) {
        $status = 'ACTIVE,INACTIVE';
    } else {
        $status = 'ACTIVE';
    }

    if (!empty($readable)) {
        $priv_level = 'READ';
    } else {
        $priv_level = 'COMMENT';
    }

    // Get all calendars the user is allowed to submit to.
    $calendars = xarModAPifunc('ievents', 'user','getcalendars', array('event_priv' => $priv_level, 'status' => $status));

    // If mandatory is present, and is not true, then include the 'any' option in the list.
    if (isset($mandatory) && !$mandatory) {
        $return = array(0 => xarML('-- Any calendar --'));
    } else {
        $return = array();
    }

    foreach($calendars as $calendar) {
        $return[$calendar['cid']] = $calendar['short_name'];
        if ($calendar['status'] == 'INACTIVE') $calendar['short_name'] .= ' ' . xarML('(Inactive)');
    }

    return $return;
}

?>
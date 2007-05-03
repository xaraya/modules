<?php

/**
 * View available calendars.
 *
 * Only calendars for which the user has overview access
 * to its events are shown.
 * Only ACTIVE calendars are shown, unless the user is an administrator.
 * There is no pager, as it is assumed there will be a small enough
 * number of calendars to handle on one screen. This may change if
 * the system is scaled up to allow each user to have their own
 * calendar.
 *
 * @todo Pager support
 * @todo Sorting and ordering
 * @todo Provide calendars with a sepatate textual summary, and full html description
 * @todo 
 */

function ievents_user_viewcals($args)
{
    extract($args);

    // The user may want to display the details of a single calendar.
    xarVarPrepForDisplay('cid', 'id', $cid, 0, XAR_VAR_NOTREQUIRED);
    xarVarPrepForDisplay('name', 'str:0:200', $name, '', XAR_VAR_NOTREQUIRED);

    // If we have admin privileges, then include inactive calendars.
    if (xarSecurityCheck('AdminIEvent', 0, 'IEvent', 'All:All:All')) {
        $status = 'ACTIVE,INACTIVE';
    } else {
        $status = 'ACTIVE';
    }

    $params = array(
        'status' => $status
    );

    // Limit to a specific calendar if requested
    if (!empty($cid)) $params['cid'] = $cid;
    if (!empty($name)) $params['name'] = $name;

    // Get the calendars.
    $calendars = xarModAPIfunc('ievents', 'user', 'getcalendars', $params);

    $return = array(
        'calendars' => $calendars,
    );

    return $return;
}

?>
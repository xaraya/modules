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
    xarVarFetch('cid', 'id', $cid, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('name', 'str:0:200', $name, '', XARVAR_NOT_REQUIRED);

    // Get module variables.
    list($cal_subscribe_range, $cal_subscribe_numitems) = xarModAPIfunc('ievents', 'user', 'params',
        array('names' => 'cal_subscribe_range,cal_subscribe_numitems')
    );

    // Get details of the export handlers available.
    $export_object = xarModAPIfunc('ievents', 'export', 'new_export');
    if (!empty($export_object)) {
        $export_handlers = $export_object->handlers;
    } else {
        $export_handlers = array();
    }

    // If we have admin privileges, then include inactive calendars.
    // TODO: this check has cropped up in a number of places now. Can they be combined?
    if (xarSecurityCheck('AdminIEvent', 0, 'IEvent', 'All:All:All')) {
        $status = 'ACTIVE,INACTIVE';
    } else {
        $status = 'ACTIVE';
    }

    $params = array(
        'status' => $status
    );

    // Get all calendars.
    $all_calendars = xarModAPIfunc('ievents', 'user', 'getcalendars', $params);

    // Limit to a specific calendar if requested
    if (!empty($cid)) $params['cid'] = $cid;
    if (!empty($name)) $params['name'] = $name;

    // Get the calendars.
    $calendars = xarModAPIfunc('ievents', 'user', 'getcalendars', $params);

    $return = array(
        'all_calendars' => $all_calendars,
        'calendars' => $calendars,
        'cal_subscribe_range' => $cal_subscribe_range,
        'export_handlers' => $export_handlers,
        'cal_subscribe_numitems' => $cal_subscribe_numitems,
    );

    return $return;
}

?>
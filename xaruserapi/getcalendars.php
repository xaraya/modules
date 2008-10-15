<?php

/**
 * Get calendars.
 *
 * @param event_priv string the level at which we want to access the calendars wrt events
 * @param status string or array the status of the calendars to be returned (null for all available)
 * @param cid int Calendar ID
 * @param cids array Calender IDs
 * @param name string Fetches calendars (though usually just one) of a given name
 */

function ievents_userapi_getcalendars($args)
{
    extract($args);

    // Fetch all the config items we need at once.
    $module = 'ievents';
    $modid = xarModGetIDFromName($module);
    $itemtype = xarModGetVar('ievents', 'itemtype_calendars');

    // Only used for some text escaping methods.
    $dbconn =& xarDBGetConn();

    // The event action privilege level for accessing the calendars returned.
    // Levels are:
    // - OVERVIEW: can see summary of events
    // - READ: can read events
    // - COMMENT: can post a new event
    // - MODERATE: can change the status of events
    // - EDIT: can edit events
    // - etc.
    if (empty($event_priv)) $event_priv = 'OVERVIEW';
    switch ($event_priv) {
        default:
        case 'OVERVIEW': $event_priv_mask = 'ReadIEvent'; break;
        case 'READ': $event_priv_mask = 'OverviewIEvent'; break;
        case 'COMMENT': $event_priv_mask = 'CommentIEvent'; break;
        case 'MODERATE': $event_priv_mask = 'ModerateIEvent'; break;
        case 'EDIT': $event_priv_mask = 'EditIEvent'; break;
        case 'DELETE': $event_priv_mask = 'EditIEvent'; break;
        case 'ADMIN': $event_priv_mask = 'AdminIEvent'; break;
    }

    $where_arr = array();

    // Calendar IDs
    if (xarVarValidate('id', $cid, true)) $cids = array($cid);
    if (!empty($cids) && xarVarValidate('list:id', $cids, true)) {
        $where_arr[] = 'cid in (' . implode(',', $cids) . ')';
    }

    // Short name
    if (!empty($name)) $where_arr[] = 'short_name eq ' . $dbconn->qstr($short_name);

    // Status selection
    // TODO: fetch the status list from the DD object.
    if (xarVarValidate('strlist:,:enum:ACTIVE:INACTIVE', $status, true)) $status = explode(',', $status);
    if (!empty($status) && xarVarValidate('list:enum:ACTIVE:INACTIVE', $status, true)) {
        $where_arr[] = "status in ('" . implode("', '", $status) . "')";
    }

    if (empty($sort)) $sort = 'short_name ASC';

    // If we are an administrator for the module, then we have
    // access to all calendars. This saves a little time checking.
    // TODO: necesary? The numbers of calendars will probably be small.

    $params = array (
        'module' => $module,
        'itemtype' => $itemtype,
        'sort' => $sort,
    );

    // Create the where-clause if we have anything to put in it.
    if (!empty($where_arr)) $where = implode(' AND ', $where_arr);
    if (!empty($where)) $params['where'] = $where;

    if (!empty($fieldlist)) $params['fieldlist'] = $fieldlist;

    $calendars = xarModAPIfunc('dynamicdata', 'user', 'getitems', $params);

    // Put the calendars we want into an array.
    $return = array();
    if (!empty($calendars)) {
        foreach($calendars as $calendar) {
            // Perform security check.
            // The check is "does the current user have at least the privilege requested"
            
            // Can we do the appropriate things to events in this calendar?
            // mask is calendar_id:event_id:event_owner_id, so we are only looking at the privileges
            // of the events in respect to the calendar they are in.
            // Don't raise any errors.
            if (!xarSecurityCheck($event_priv_mask, 0, 'IEvent', $calendar['cid'] . ':All:All')) break;

            $return[$calendar['cid']] = $calendar;
        }
    }

    return $return;
}

?>

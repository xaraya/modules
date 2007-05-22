<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage calendar
 * @author andrea.m
 */

/**
 * Delete a calendar from database
 * Usage : if (xarModAPIFunc('calendar', 'admin', 'delete', $calendar)) {...}
 *
 * @param $args['calid'] ID of the calendar
 * @returns bool
 * @return true on success, false on failure
 */

function calendar_adminapi_delete_calendar($args)
{


    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($calid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'calendar ID', 'admin', 'delete',
                    'Calendar');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

    // TODO: Security check
/*
    if (!xarModAPILoad('calendar', 'user')) return;

    $args['mask'] = 'DeleteCalendars';
    if (!xarModAPIFunc('calendar','user','checksecurity',$args)) {
        $msg = xarML('Not authorized to delete #(1) items',
                    'Calendar');
        xarErrorSet(XAR_USER_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return false;
    }
*/
    // Call delete hooks for categories, hitcount etc.
    $args['module'] = 'calendar';
    $args['itemid'] = $calid;
    xarModCallHooks('item', 'delete', $calid, $args);

    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    $calendarstable = $xartable['calendars'];
    $cal_filestable = $xartable['calendars_files'];
    $calfiles = $xartable['calfiles'];

    // Get files associated with that calendar
    $query ="SELECT xar_files_id FROM $cal_filestable
             WHERE xar_calendars_id = ? LIMIT 1 ";
    $result =& $dbconn->Execute($query, array($calid));
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        // there should be only one result
        list($file_id) = $result -> fields;
    }

    if (isset($file_id) || !empty($file_id)) {
        $query = "DELETE FROM $calfiles
                  WHERE xar_id = ?";
        $result =& $dbconn->Execute($query, array($file_id));
        if (!$result) return;
    }

    // Delete item
    $query = "DELETE FROM $calendarstable
              WHERE xar_id = ?";
    $result =& $dbconn->Execute($query, array($calid));
    if (!$result) return;

    $query = "DELETE FROM $cal_filestable
              WHERE xar_calendars_id = ?";
    $result =& $dbconn->Execute($query, array($calid));
    if (!$result) return;

    $result -> Close();

    return true;
}

?>
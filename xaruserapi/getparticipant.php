<?php
/**
 * File: $Id:
 * 
 * Get all module items
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage courses
 * @author Courses module development team 
 */
/**
 * get a single participant of a planned course
 * 
 * @author the Courses module development team 
 * @param sid $ the ID of the student/participant
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function courses_userapi_getparticipant($args)
{
    extract($args);
    if (!xarVarFetch('sid', 'int:1:', $sid)) return;

    $item = array();
    if (!xarSecurityCheck('EditPlanning')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $studentstable = $xartable['courses_students'];
    // TODO: how to select by cat ids (automatically) when needed ???
    $query = "SELECT xar_sid,
                     xar_userid,
                     xar_planningid,
                     xar_status,
                     xar_regdate
            FROM $studentstable
            WHERE xar_sid = ?";
            
    $result = $dbconn->Execute($query, array((int)$sid));
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This participant does not exists');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }
    // Put item into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($sid, $userid, $planningid, $status, $regdate) = $result->fields;
        if (xarSecurityCheck('ViewPlanning', 0, 'Planning', "$planningid:All:All")) { //TODO
            $item = array('sid'        => $sid,
                          'userid'     => $userid,
                          'planningid' => $planningid,
                          'status'     => $status,
                          'regdate'    => $regdate);
        }
    }
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();
    // Return the items
    return $item;
}

?>

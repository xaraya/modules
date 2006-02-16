<?php
/**
 * Get a specific participant
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * get a single participant of a planned course
 *
 * @author the Courses module development team
 * @param id sid The ID of the student/participant
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function courses_userapi_getparticipant($args)
{
    extract($args);
    if (!xarVarFetch('sid', 'int:1:', $sid)) return;

    $item = array();
    if (!xarSecurityCheck('EditCourses')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $studentstable = $xartable['courses_students'];

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
        list($sid, $userid, $planningid, $studstatus, $regdate) = $result->fields;
        if (xarSecurityCheck('ReadCourses', 0, 'Course', "All:$planningid:All")) { //TODO
            $item = array('sid'        => $sid,
                          'userid'     => $userid,
                          'planningid' => $planningid,
                          'studstatus' => $studstatus,
                          'regdate'    => $regdate);
        }
    }
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();
    // Return the item
    return $item;
}

?>

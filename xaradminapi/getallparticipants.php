<?php
/**
 * File: $Id:
 * 
 * Get all module items
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage courses
 * @author Courses module development team 
 */
/**
 * get all participants for a planned course
 * 
 * @author the Courses module development team 
 * @param numitems $ the number of items to retrieve (default -1 = all)
 * @param startnum $ start with this item number (default 1)
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function courses_adminapi_getallparticipants($args)
{
    extract($args);
    if (!xarVarFetch('planningid', 'int:1:', $planningid)) return;
    if (!xarVarFetch('startnum', 'int:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('numitems', 'int:1:', $numitems, '-1', XARVAR_NOT_REQUIRED)) return;

    $items = array();
    if (!xarSecurityCheck('EditCourses', '1', 'Course', "All:$planningid:All")) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $studentstable = $xartable['courses_students'];
    // TODO: how to select by cat ids (automatically) when needed ???
    // Get items
    $query = "SELECT xar_sid,
                     xar_userid,
                     xar_planningid,
                     xar_status,
                     xar_regdate
              FROM $studentstable
              WHERE xar_planningid = $planningid
              ORDER BY xar_sid";
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Put items into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($sid, $userid, $planningid, $status, $regdate) = $result->fields;
        if (xarSecurityCheck('EditCourses', 0, 'Course', "All:$planningid:All")) { 
            $items[] = array('sid'        => $sid,
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
    return $items;
}

?>

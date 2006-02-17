<?php
/**
 * Get all courses for one student
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */

/**
 * Get all courses that a student is enrolled to
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param numitems $ the number of items to retrieve (default -1 = all)
 * @param startnum $ start with this item number (default 1)
 * @return array of items, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function courses_userapi_getall_enrolled($args)
{
    extract($args);

    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('numitems', 'int:1:', $numitems, -1, XARVAR_NOT_REQUIRED)) return;

    $uid = xarUserGetVar('uid');
    $items = array();
    // Security check
    if (!xarSecurityCheck('ReadCourses')) return;
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $planningtable = $xartable['courses_planning'];
    $coursestable = $xartable['courses'];
    $studentstable = $xartable['courses_students'];
    // TODO: how to select by cat ids (automatically) when needed ???
    $query = "SELECT $coursestable.xar_name,
            $coursestable.xar_courseid,
            $planningtable.xar_planningid,
            $planningtable.xar_startdate,
            $planningtable.xar_hideplanning,
            $studentstable.xar_status,
            $studentstable.xar_regdate
            FROM $studentstable, $coursestable
            JOIN $planningtable
            ON $planningtable.xar_planningid = $studentstable.xar_planningid
            WHERE $studentstable.xar_userid = $uid
            AND $coursestable.xar_courseid = $planningtable.xar_courseid";
            //AND $planningtable.xar_planningid = $studentstable.xar_planningid
     $result = &$dbconn->Execute($query);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Put items into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($name, $courseid, $planningid, $startdate, $hideplanning, $studstatus, $regdate) = $result->fields;
        if (xarSecurityCheck('ReadCourses', 0, 'Course', "$courseid:$planningid:All")) {
            $items[] = array('name'         => $name,
                             'courseid'     => $courseid,
                             'planningid'   => $planningid,
                             'startdate'    => $startdate,
                             'hideplanning' => $hideplanning,
                             'studstatus'   => $studstatus,
                             'regdate'      => $regdate);
        }
    }
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();
    // Return the items
    return $items;
}
?>

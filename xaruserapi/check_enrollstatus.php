<?php
/**
 * Check the status of a student in one specific course
 *
 * @package modules
 * @copyright (C) 2006-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * Get information about the status of a student for a certain course
 *
 * This function will check to see if a student is enrolled for a course, and
   if so, what the status is, and return all relevant information
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param int courseid The ID of the course the look for
 * @param int userid The ID of the user that is a student
 * @since 28 Aug 2006
 * @return array of items, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function courses_userapi_check_enrollstatus($args)
{
    extract($args);

    if (!isset($userid) || !is_numeric($userid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'user ID', 'user', 'check_enrollstatus', 'Courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    if (!isset($courseid) || !is_numeric($courseid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'course ID', 'user', 'check_enrollstatus', 'Courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $items = array();
    // Security check
    if (!xarSecurityCheck('ReadCourses')) return;
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $planningtable = $xartable['courses_planning'];
    $studentstable = $xartable['courses_students'];
    // Build the query
    $query = "SELECT
            $planningtable.xar_planningid,
            $planningtable.xar_startdate,
            $planningtable.xar_hideplanning,
            $planningtable.xar_credits,
            $studentstable.xar_status,
            $studentstable.xar_regdate
            FROM $planningtable
            JOIN $studentstable
            ON ($planningtable.xar_planningid = $studentstable.xar_planningid)
            WHERE ($studentstable.xar_userid = $userid
            AND $planningtable.xar_courseid = $courseid)";
     $result = &$dbconn->Execute($query);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Put items into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($planningid, $startdate, $hideplanning, $credits, $studstatus, $regdate) = $result->fields;
        if (xarSecurityCheck('ReadCourses', 0, 'Course', "$courseid:$planningid:All")) {
            $items[] = array('planningid'   => $planningid,
                             'startdate'    => $startdate,
                             'hideplanning' => $hideplanning,
                             'credits'      => $credits,
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

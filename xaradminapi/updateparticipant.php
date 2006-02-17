<?php
/**
 * Update a participant
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
 * update the enrollement for a student
 *
 * @author the Courses module development team
 * @param  $args ['sid'] id of student/participant
 * @param  $args ['planningid'] number of the planned course
 * @param  $args ['statusid'] status of the student enrolling
 * @returns int
 * @return enroll ID on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function courses_adminapi_updateparticipant($args)
{
    extract($args);
    if (!xarVarFetch('planningid', 'id', $planningid)) return;
    if (!xarVarFetch('sid', 'id', $sid)) return;
    if (!xarVarFetch('statusid', 'int:1:', $statusid)) return;

    $invalid = array();
     if (!isset($sid) || !is_numeric($sid)) {
        $invalid[] = 'sid';
    }
     if (!isset($planningid) || !is_numeric($planningid)) {
        $invalid[] = 'planningid';
    }
     if (!isset($statusid) || !is_numeric($statusid)) {
        $invalid[] = 'statusid';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'create_enroll', 'Courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
    if (!xarSecurityCheck('EditCourses', 1, 'Course', "All:$planningid:All")) { //Correct?
        return;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $studentstable = $xartable['courses_students'];
    // Add item
    $query = "UPDATE $studentstable
              SET xar_planningid = ?,
                  xar_status = ?
              WHERE xar_sid = ?
              ";
    $bindvars = array($planningid, $statusid, $sid);
    $result = &$dbconn->Execute($query, $bindvars);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;

    // Return the id of the newly created item to the calling process
    return $sid;
}

?>

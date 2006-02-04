<?php
/**
 * Update a planned course
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * update a planned course
 *
 * @author the Courses module development team
 * @param  $args ['planningid'] the ID of the course
 * @param  $args ['name'] the new name of the item
 * @param  $args ['number'] the new number of the item
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function courses_adminapi_updateplanned($args)
{
    extract($args);

    // Get parameters from whatever input we need.
    if (!xarVarFetch('planningid', 'id', $planningid)) return;
    if (!xarVarFetch('name', 'str:1:', $name, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('number', 'str:1:', $number, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('coursetype', 'str:1:', $coursetype, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('level', 'int:1:', $level, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('year', 'int:1:', $year, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('credits', 'int::', $credits, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('creditsmin', 'int::', $creditsmin, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('creditsmax', 'int::', $creditsmax, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shortdesc', 'str:1:', $shortdesc, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('prerequisites', 'str:1:', $prerequisites, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('aim', 'str:1:', $aim, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('method', 'str:1:', $method, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('language', 'str:1:', $language, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('location', 'str:1:', $location, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('costs', 'str:1:', $costs, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('material', 'str:1:', $material, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hideplanning', 'int:1:', $hideplanning, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('info', 'str:1:', $info, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'str::', $invalid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('minparticipants', 'int::', $minparticipants, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('maxparticipants', 'int::', $maxparticipants, '', XARVAR_NOT_REQUIRED)) return;

    // The user API function is called.
    $item = xarModAPIFunc('courses',
        'user',
        'getplanned',
        array('planningid' => $planningid));
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    $courseid = $item['courseid'];
    // Security check
    if (!xarSecurityCheck('EditCourses', 1, 'Course', "$courseid:$planningid:All")) {
        return;
    }
    // Convert date strings to int format
    if (!empty($startdate) && !is_numeric($startdate)) {
        $startdate = strtotime($startdate);
    }
    if (!empty($enddate) && !is_numeric($enddate)) {
        $enddate = strtotime($enddate);
    }
    if (!empty($closedate) && !is_numeric($closedate)) {
        $closedate = strtotime($closedate);
    }
    $last_modified = time();

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $planningtable = $xartable['courses_planning'];
    // Update the item
    $query = "UPDATE $planningtable
                       SET xar_planningid =?,
                           xar_courseid =?,
                           xar_courseyear =?,
                           xar_credits =?,
                           xar_creditsmin =?,
                           xar_creditsmax =?,
                           xar_startdate =?,
                           xar_enddate =?,
                           xar_prerequisites =?,
                           xar_aim =?,
                           xar_method =?,
                           xar_longdesc =?,
                           xar_costs =?,
                           xar_committee =?,
                           xar_coordinators =?,
                           xar_lecturers =?,
                           xar_location =?,
                           xar_material =?,
                           xar_info =?,
                           xar_program =?,
                           xar_minparticipants =?,
                           xar_maxparticipants =?,
                           xar_closedate =?,
                           xar_hideplanning =?,
                           xar_last_modified =?
                        WHERE xar_planningid = $planningid";

    $bindvars = array($planningid, $courseid, $year, $credits, $creditsmin, $creditsmax, $startdate, $enddate, $prerequisites, $aim, $method, $longdesc,
                      $costs, $committee, $coordinators, $lecturers, $location, $material, $info, $program, $minparticipants,
                      $maxparticipants, $closedate, $hideplanning, $last_modified);
    $result = &$dbconn->Execute($query, $bindvars);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;

    // Let the calling process know that we have finished successfully
    return true;
}

?>

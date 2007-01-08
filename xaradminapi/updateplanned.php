<?php
/**
 * Update a planned course
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
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
 * @author MichelV <michelv@xarayahosting.nl>
 * @param  $args ['planningid'] the ID of the course
 * @param  $args ['name'] the new name of the item
 * @param  $args ['number'] the new number of the item
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function courses_adminapi_updateplanned($args)
{
    extract($args);

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
                           xar_expected =?,
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
                           xar_extreg =?,
                           xar_regurl =?,
                           xar_minparticipants =?,
                           xar_maxparticipants =?,
                           xar_closedate =?,
                           xar_hideplanning =?,
                           xar_last_modified =?
                        WHERE xar_planningid = $planningid";

    $bindvars = array($planningid,
                      $courseid,
                      $year,
                      $credits,
                      $creditsmin,
                      $creditsmax,
                      $startdate,
                      $enddate,
                      $expected,
                      $prerequisites,
                      $aim,
                      $method,
                      $longdesc,
                      $costs,
                      $committee,
                      $coordinators,
                      $lecturers,
                      $location,
                      $material,
                      $info,
                      $program,
                      $extreg  ? 1 : 0,
                      $regurl,
                      $minparticipants,
                      $maxparticipants,
                      $closedate,
                      $hideplanning  ? 1 : 0,
                      $last_modified);
    $result = &$dbconn->Execute($query, $bindvars);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;

    // Let the calling process know that we have finished successfully
    return true;
}

?>

<?php
/**
 * Get all dates that a course is planned
 *
 * @package modules
 * @copyright (C) 2005-2006 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * get all dates that one course is planned
 *
 * @author the Courses module development team
 * @param id courseid The course to get all the dates for
 * @param numitems $ the number of items to retrieve (default -1 = all)
 * @param startnum $ start with this item number (default 1)
 * @param after The date (int) for which the closedate should be after
 * @return array of items, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function courses_userapi_getplandates($args)
{
    extract($args);
    if (!xarVarFetch('courseid', 'id',     $courseid)) return;
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('numitems', 'int:1:', $numitems, -1, XARVAR_NOT_REQUIRED)) return;

    $items = array();
    // Security check
    if (!xarSecurityCheck('ReadCourses')) return;

    if (xarSecurityCheck('EditCourses', 0)) {
        $where = "0, 1";
    } else {
        $where = "0";
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $planningtable = $xartable['courses_planning'];
    // TODO: implement security check when this item is hidden from display
    // TODO: how to select by cat ids (automatically) when needed ???
    // Get items
    $query = "SELECT xar_planningid,
               xar_courseid,
               xar_credits,
               xar_creditsmin,
               xar_creditsmax,
               xar_courseyear,
               xar_startdate,
               xar_enddate,
               xar_prerequisites,
               xar_aim,
               xar_method,
               xar_language,
               xar_longdesc,
               xar_costs,
               xar_committee,
               xar_coordinators,
               xar_lecturers,
               xar_location,
               xar_material,
               xar_info,
               xar_program,
               xar_hideplanning,
               xar_last_modified,
               xar_closedate
        FROM $planningtable
        WHERE xar_courseid = $courseid AND xar_hideplanning in ($where)";
    // Look for the courses planned after $after
    if (isset($startafter) && is_numeric($startafter)) {
        $query .= " AND xar_startdate > $startafter ";
    }
    if (isset($closeafter) && is_numeric($closeafter)) {
        $query .= " AND xar_closedate > $closeafter ";
    }

    $query .= " ORDER BY xar_startdate";
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Put items into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($planningid, $courseid, $credits, $creditsmin, $creditsmax, $courseyear, $startdate, $enddate,
             $prerequisites, $aim, $method, $language, $longdesc, $costs, $committee, $coordinators, $lecturers,
             $location, $material, $info, $program, $hideplanning, $last_modified, $closedate)
             = $result->fields;
        if (xarSecurityCheck('ReadCourses', 0, 'Course', "$courseid:$planningid:$courseyear")) {
            $items[] = array(
            'planningid'    => $planningid,
            'courseid'      => $courseid,
            'credits'       => $credits,
            'creditsmin'    => $creditsmin,
            'creditsmax'    => $creditsmax,
            'courseyear'    => $courseyear,
            'startdate'     => $startdate,
            'enddate'       => $enddate,
            'prerequisites' => $prerequisites,
            'aim'           => $aim,
            'method'        => $method,
            'language'      => $language,
            'longdecr'      => $longdesc,
            'costs'         => $costs,
            'committee'     => $committee,
            'lecturers'     => $lecturers,
            'location'      => $location,
            'material'      => $material,
            'info'          => $info,
            'program'       => $program,
            'hideplanning'  => $hideplanning,
            'last_modified' => $last_modified,
            'closedate'     => $closedate);
        }
    }
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();
    // Return the items
    return $items;
}

?>

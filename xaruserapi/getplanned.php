<?php
/**
 * Get a planned course
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * get a specific planned course
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param int planningid $ ID of a specific planned course
 * @return array item with array of parameters, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function courses_userapi_getplanned($args)
{
    extract($args);
    if (!isset($planningid) || !is_numeric($planningid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'userapi', 'getplanned', 'courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $items = array();

    // Security check
    if (!xarSecurityCheck('ReadCourses', '0', 'Course', "All:$planningid:All")) return;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $planningtable = $xartable['courses_planning'];

    // TODO: implement security check when this item is hidden from display
    // Get item
        $query = "SELECT xar_planningid,
                   xar_courseid,
                   xar_credits,
                   xar_creditsmin,
                   xar_creditsmax,
                   xar_courseyear,
                   xar_startdate,
                   xar_enddate,
                   xar_expected,
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
                   xar_regurl,
                   xar_extreg,
                   xar_hideplanning,
                   xar_minparticipants,
                   xar_maxparticipants,
                   xar_closedate,
                   xar_last_modified
            FROM $planningtable
            WHERE xar_planningid = ?";
    $result = $dbconn->Execute($query, array($planningid));
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Put items into result array.
        list($planningid, $courseid, $credits, $creditsmin, $creditsmax, $courseyear, $startdate, $enddate,$expected,
         $prerequisites, $aim, $method, $language, $longdesc, $costs, $committee, $coordinators, $lecturers,
          $location, $material, $info, $program, $regurl, $extreg, $hideplanning, $minparticipants, $maxparticipants, $closedate, $last_modified) = $result->fields;
        if (xarSecurityCheck('ReadCourses', 0, 'Course', "$courseid:$planningid:$courseyear")) {
            $item = array(
                        'planningid'    => $planningid,
                        'courseid'      => $courseid,
                        'credits'       => $credits,
                        'creditsmin'    => $creditsmin,
                        'creditsmax'    => $creditsmax,
                        'courseyear'    => $courseyear,
                        'startdate'     => $startdate,
                        'enddate'       => $enddate,
                        'expected'      => $expected,
                        'prerequisites' => $prerequisites,
                        'aim'           => $aim,
                        'method'        => $method,
                        'language'      => $language,
                        'longdesc'      => $longdesc,
                        'costs'         => $costs,
                        'committee'     => $committee,
                        'coordinators'  => $coordinators,
                        'lecturers'     => $lecturers,
                        'location'      => $location,
                        'material'      => $material,
                        'info'          => $info,
                        'program'       => $program,
                        'regurl'          => $regurl,
                        'extreg'          => $extreg,
                        'hideplanning'    => $hideplanning,
                        'minparticipants' => $minparticipants,
                        'maxparticipants' => $maxparticipants,
                        'closedate'       => $closedate,
                        'last_modified'   => $last_modified
                        );
        }
    //}
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();
    // Return the items
    return $item;
}

?>
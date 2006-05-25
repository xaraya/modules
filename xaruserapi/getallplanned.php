<?php
/**
 * Get all planned courses
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
 * get all planned courses
 *
 * @author the Courses module development team
 * @param int numitems $ the number of items to retrieve (default -1 = all)
 * @param int startnum $ start with this item number (default 1)
 * @param int catid $ category id for this planned course
 * @param str sortby $ what to sort by (default planningid)
 * @param str sortorder $how to sort (default DESC)
 * @return array of items, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 * @todo MichelV <1> rewrite category call as categories are hooked to coursetypes now
 */
function courses_userapi_getallplanned($args)
{
    extract($args);

    $items = array();
    // Security check
    if (!xarSecurityCheck('ReadCourses')) return;

    if (!isset($sortby) || (isset($sortby) && !in_array($sortby,array('planningid','courseid','credits','courseyear','startdate')))) {
        $sortby ='startdate';
    }
    if (!isset($startnum) || !is_numeric($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $numitems = -1;
    }
    if (!isset($sortorder) || (isset($sortorder) && !in_array($sortorder, array('DESC','ASC')))) {
        $sortorder = 'DESC';
    }
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $planningtable = $xartable['courses_planning'];
    // TODO: how to select by cat ids (automatically) when needed ???

    // Set to be able to see all courses or only non-hidden ones
    if (xarSecurityCheck('EditCourses', 0)) {
    $where = "0, 1";
    } else {
    $where = "0";
    }

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
                   xar_regurl,
                   xar_extreg,
                   xar_hideplanning,
                   xar_minparticipants,
                   xar_maxparticipants,
                   xar_closedate,
                   xar_hideplanning,
                   xar_last_modified";

    // TODO: how to select by cat ids (automatically) when needed ???
    // 2=planned courses
    if (!empty($catid) && xarModIsHooked('categories','courses')) {
        // Get the LEFT JOIN ... ON ...  and WHERE parts from categories
        $categoriesdef = xarModAPIFunc('categories','user','leftjoin',
                                       array('modid' => xarModGetIDFromName('courses'),
                                             'catid' => $catid));
        if (!empty($categoriesdef)) {
            $query .= " FROM ($planningtable
                        LEFT JOIN $categoriesdef[table]
                        ON $categoriesdef[field] = xar_planningid )
                        $categoriesdef[more]
                        WHERE $categoriesdef[where]
                        AND xar_hideplanning in ($where)";
            } else {
                $query .= " FROM $planningtable
                            WHERE xar_hideplanning in ($where)";
            }
     } else {
        $query .= " FROM $planningtable
                    WHERE xar_hideplanning in ($where)";
     }

    $query .= " ORDER BY $planningtable.xar_" . $sortby ;
    $query .= " $sortorder";

    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Put items into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($planningid, $courseid, $credits, $creditsmin, $creditsmax, $courseyear, $startdate, $enddate,
         $prerequisites, $aim, $method, $language, $longdesc, $costs, $committee, $coordinators, $lecturers,
          $location, $material, $info, $program, $regurl, $exturl, $hideplanning, $minparticipants, $maxparticipants, $closedate, $hideplanning, $last_modified) = $result->fields;
        if (xarSecurityCheck('ReadCourses', 0, 'Course', "$courseid:$planningid:$courseyear")){

            $items[] = array(
            'planningid' => $planningid,
            'courseid'   => $courseid,
            'credits'    => $credits,
            'creditsmin' => $creditsmin,
            'creditsmax' => $creditsmax,
            'courseyear' => $courseyear,
            'startdate'  => $startdate,
            'enddate'    => $enddate,
            'prerequisites' => $prerequisites,
            'aim'        => $aim,
            'method'     => $method,
            'language'   => $language,
            'longdecr'   => $longdesc,
            'costs'      => $costs,
            'committee'  => $committee,
            'lecturers'  => $lecturers,
            'location'   => $location,
            'material'   => $material,
            'info'       => $info,
            'program'    => $program,
            'regurl'          => $regurl,
            'extreg'          => $extreg,
            'hideplanning' => $hideplanning,
            'minparticipants' => $minparticipants,
            'maxparticipants' => $maxparticipants,
            'closedate' => $closedate,
            'hideplanning'  => $hideplanning,
            'last_modified' => $last_modified);
        }
    }
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();
    // Return the items
    return $items;
}

?>
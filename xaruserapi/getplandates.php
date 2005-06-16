<?php
/**
 * File: $Id:
 * 
 * Get all planned courses
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage courses
 * @author Courses module development team 
 */
/**
 * get all dates that course is planned
 * 
 * @author the Courses module development team 
 * @param numitems $ the number of items to retrieve (default -1 = all)
 * @param startnum $ start with this item number (default 1)
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function courses_userapi_getplandates($args)
{
    extract($args);
    if (!isset($courseid) || !is_numeric($courseid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'getplandates', 'courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }
    // Argument check
    
    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }

    
    $items = array();
    
    // Security check
    if (!xarSecurityCheck('ViewPlanning')) return;
    
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // It's good practice to name the table definitions you are
    // using - $table doesn't cut it in more complex modules
    $planningtable = $xartable['courses_planning'];
    
    //TODO: implement security check when this item is hidden from display
    
    // TODO: how to select by cat ids (automatically) when needed ???
    
    // Get item 
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
                   xar_longdesc,
                   xar_costs,
                   xar_committee,
                   xar_coordinators,
                   xar_lecturers,
                   xar_location,
                   xar_material,
                   xar_info,
                   xar_program,
                   xar_hideplanning
            FROM $planningtable
            WHERE xar_courseid = $courseid
            ORDER BY xar_startdate";
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Put items into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($planningid, $courseid, $credits, $creditsmin, $creditsmax, $courseyear, $startdate, $enddate,
         $prerequisites, $aim, $method, $longdesc, $costs, $committee, $coordinators, $lecturers,
          $location, $material, $info, $program, $hideplanning) = $result->fields;
        if (xarSecurityCheck('ViewPlanning', 0, 'Item', "$planningid:$courseyear:$courseid")) {
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
            'longdecr'   => $longdesc,
            'costs'      => $costs,
            'committee'  => $committee,
            'lecturers'  => $lecturers,
            'location'   => $location,
            'material'   => $material,
            'info'       => $info,
            'program'    => $program,
            'hideplanning' => $hideplanning);
        }
    }
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();
    // Return the items
    return $items;
}

?>
<?php
/**
 * File: $Id:
 * 
 * Get all module items
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
 * get all courses names that a student is enrolled to
 * 
 * @author Michel V. 
 * @param numitems $ the number of items to retrieve (default -1 = all)
 * @param startnum $ start with this item number (default 1)
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function courses_userapi_getall_enrolled($args)
{
    extract($args);
    
    if (!xarVarFetch('startnum', 'int:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('numitems', 'int:1:', $numitems, '-1', XARVAR_NOT_REQUIRED)) return;
/*
 * This will be obsolete
    // Optional arguments.
    // FIXME: (!isset($startnum)) was ignoring $startnum as it contained a null value
    // replaced it with ($startnum == "") (thanks for the talk through Jim S.) NukeGeek 9/3/02
    // if (!isset($startnum)) {
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }
    // Argument check - make sure that all required arguments are present and
    // in the right format, if not then set an appropriate error message
    // and return
    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'getall_enrolled', 'courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
*/

    $uid = xarUserGetVar('uid');
    $items = array();
    // Security check
    if (!xarSecurityCheck('ViewPlanning')) return;
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
            $studentstable.xar_status
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
        list($name, $courseid, $planningid, $startdate, $studstatus) = $result->fields;
        if (xarSecurityCheck('ViewPlanning', 0, 'Planning', "$planningid:All:$courseid")) {
            $items[] = array('name' => $name,
                             'courseid'=> $courseid,
                             'planningid' => $planningid,
                             'startdate'=> $startdate,
                             'studstatus'=> $studstatus);
        }
    }
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();
    // Return the items
    return $items;
}
?>

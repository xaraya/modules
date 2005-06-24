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
 * get all teachers for a planned course
 * 
 * @author the Courses module development team 
 * @param numitems $ the number of items to retrieve (default -1 = all)
 * @param startnum $ start with this item number (default 1)
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function courses_adminapi_getallteachers($args)
{
    extract($args);
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
    // Note : since we have several arguments we want to check here, we'll
    // report all those that are invalid at the same time...
    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (!isset($planningid) || !is_numeric($planningid)) {
        $invalid[] = 'planningid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'getallteachers', 'courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $items = array();
    if (!xarSecurityCheck('EditPlanning')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $teacherstable = $xartable['courses_teachers'];
    // TODO: how to select by cat ids (automatically) when needed ???
    // Get items
    $query = "SELECT xar_tid,
                   xar_userid,
                   xar_planningid,
                   xar_type
            FROM $teacherstable
            WHERE xar_planningid = $planningid
            ORDER BY xar_tid";
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Put items into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($tid, $userid, $planningid, $type) = $result->fields;
        if (xarSecurityCheck('ViewPlanning', 0, 'Planning', "All:All:All")) { //TODO
            $items[] = array('tid' => $tid,
                'userid'           => $userid,
                'planningid'       => $planningid,
                'type'             => $type);
        }
    }
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();
    // Return the items
    return $items;
}

?>

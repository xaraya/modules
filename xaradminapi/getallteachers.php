<?php
/**
 * Get all teachers for one planned course
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * get all teachers for a planned course
 *
 * @author the Courses module development team
 * @param numitems $ the number of items to retrieve (default -1 = all)
 * @param startnum $ start with this item number (default 1)
 * @param planningid $ ID of planned course
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function courses_adminapi_getallteachers($args)
{
    extract($args);
    // Optional arguments.
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
    if (!xarSecurityCheck('EditCourses', '0', 'Course', "All:$planningid:All")) return;

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
    if (!$result) return;
    // Put items into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($tid, $userid, $planningid, $teachertype) = $result->fields;
        if (xarSecurityCheck('EditCourses', 0, 'Course', "All:$planningid:All")) { //TODO
            $items[] = array('tid' => $tid,
                'userid'           => $userid,
                'planningid'       => $planningid,
                'teachertype'      => $teachertype);
        }
    }
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();
    // Return the items
    return $items;
}

?>

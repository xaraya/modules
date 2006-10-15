<?php
/**
 * get all course and names that a teacher is linked to
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
 * get all course and names that a teacher is linked to
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param numitems $ the number of items to retrieve (default -1 = all)
 * @param startnum $ start with this item number (default 1)
 * @param int uid User ID OPTIONAL
 * @return array of items, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function courses_userapi_getall_teaching($args)
{
    extract($args);
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('numitems', 'int:1:', $numitems, 20, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('uid',      'int:1:', $uid, xarUserGetVar('uid'), XARVAR_NOT_REQUIRED)) return;

    $items = array();
    // Security check
    if (!xarSecurityCheck('ViewCourses')) return;
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $coursestable = $xartable['courses'];
    $planningtable = $xartable['courses_planning'];
    $teacherstable = $xartable['courses_teachers'];
    // TODO: how to select by cat ids (automatically) when needed ???
    $query = "SELECT $coursestable.xar_name,
            $coursestable.xar_courseid,
            $planningtable.xar_planningid,
            $planningtable.xar_startdate,
            $teacherstable.xar_type
            FROM $teacherstable, $coursestable, $planningtable

            WHERE $teacherstable.xar_userid = $uid AND
                  $planningtable.xar_planningid = $teacherstable.xar_planningid AND
                  $planningtable.xar_courseid = $coursestable.xar_courseid";
     $result = &$dbconn->Execute($query);
//            JOIN $planningtable
//            ON $planningtable.xar_planningid = $teacherstable.xar_planningid
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Put items into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($name, $courseid, $planningid, $startdate, $type) = $result->fields;
        if (xarSecurityCheck('ReadCourses', 0, 'Course', "$courseid:$planningid:All")) {
            $items[] = array('name'       => $name,
                             'courseid'   => $courseid,
                             'planningid' => $planningid,
                             'startdate'  => $startdate,
                             'type'       => $type);
        }
    }
    $result->Close();
    // Return the items
    return $items;
}
?>

<?php
/**
 * Get all courses that a user is registered at as a coordinator.
 *
 * @package modules
 * @copyright (C) 2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * Get all courses that a user is registered at as a coordinator.
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @since 15 Oct 2006
 * @param numitems $ the number of items to retrieve (default -1 = all)
 * @param startnum $ start with this item number (default 1)
 * @param int uid User ID OPTIONAL
 * @return array of items, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function courses_userapi_getall_contacts($args)
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
    $query = "SELECT xar_courseid,
                    xar_name
            FROM $coursestable
            WHERE xar_contactuid = $uid
            ORDER BY xar_name";

    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);

    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Put items into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($courseid, $name,) = $result->fields;
        if (xarSecurityCheck('ReadCourses', 0, 'Course', "$courseid:All:All")) {
            $items[] = array('courseid'   => $courseid,
                             'name'       => $name);
        }
    }
    $result->Close();
    // Return the items
    return $items;
}
?>

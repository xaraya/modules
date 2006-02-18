<?php
/**
 * Get all names of courses
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
 * get the names of all courses
 *
 * @author the Courses module development team
 * @param  int numitems $ the number of items to retrieve (default -1 = all)
 * @param  int startnum $ start with this item number (default 1)
 * @return mixed array of items, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function courses_userapi_getall_names($args)
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

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'getall_names', 'courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    $names = array();
    if (!xarSecurityCheck('ViewCourses')) return;
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $coursestable = $xartable['courses'];
    $uid = xarUserGetVar('uid');
    $query = "SELECT xar_name
            FROM $coursestable
            WHERE xar_courseid = ?";
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1, array($courseid));
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Put items into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($name) = $result->fields;
        if (xarSecurityCheck('ViewCourses', 0, 'Course', "$courseid:All:All")) {
            $items[] = array('name' => $name);
       }
    }

    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();
    // Return the items
var_dump($names);
    return $names;
}

?>

<?php
/**
 * Get the id of a course when the number is known
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
 * Get a specific courseID for a given coursenumber
 *
 * @author the Courses module development team
 * @param  $args ['number'] The code of the course to get
 * @return item array with courseid and data on the hidden status, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 * @todo MichelV: <1>Extend to search for planningid ?
 */
function courses_userapi_getcourseid($args)
{
    extract($args);

    if (!isset($number)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'getcourseid', 'courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    if (xarSecurityCheck('AdminCourses', 0)) {
    $where = "0, 1";
    } else {
    $where = "0";
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $coursestable = $xartable['courses'];
    $query = "SELECT xar_courseid,
                     xar_hidecourse
              FROM $coursestable
              WHERE xar_number = ? AND xar_hidecourse in ($where)";
    $result = &$dbconn->Execute($query, array($number));
    if (!$result) return;
    // Check for no rows found, and if so, close the result set and return an exception
    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This course does not exists');
//        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
//            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return false;
    }
    // Extract fields
    list($courseid, $hidecourse) = $result->fields;
    $result->Close();

    // Security checks
    // For this function, the user must *at least* have READ access to this item
    if (!xarSecurityCheck('ReadCourses', 1, 'Course', "$courseid:All:All")) {
        return;
    }
    $item = array('courseid'   => $courseid,
                  'hidecourse' => $hidecourse);
    // Return the item array
    return $item;
}

?>

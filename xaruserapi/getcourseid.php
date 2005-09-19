<?php
/**
 * Get the id of a course when the number is known
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
 * get a specific courseID for a given coursenumber
 * 
 * @author the Courses module development team 
 * @param  $args ['number'] The code of the course to get
 * @returns courseid
 * @return item array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
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
        return;
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

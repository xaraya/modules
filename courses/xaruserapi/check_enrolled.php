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
 * @author XarayaGeek
 */
/**
 * get all example items
 *
 * @param numitems $ the number of items to retrieve (default -1 = all)
 * @param startnum $ start with this item number (default 1)
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function courses_userapi_check_enrolled($args)
{
    // Get arguments from argument array - all arguments to this function
    // should be obtained from the $args array, getting them from other places
    // such as the environment is not allowed, as that makes assumptions that
    // will not hold in future versions of Xaraya
	extract($args);
    if (!isset($courseid) || !is_numeric($courseid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'check_enrolled', 'courses');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $uid = xarUserGetVar('uid');
    $items = array();
    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
    if (!xarSecurityCheck('ViewCourses')) return;
    // Get database setup - note that both xarDBGetConn() and xarDBGetTables()
    // return arrays but we handle them differently.  For xarDBGetConn() we
    // currently just want the first item, which is the official database
    // handle.  For xarDBGetTables() we want to keep the entire tables array
    // together for easy reference later on
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // It's good practice to name the table definitions you are
    // using - $table doesn't cut it in more complex modules
	$coursestable = $xartable['courses'];
    $courses_studentstable = $xartable['courses_students'];

    $sql = "SELECT $coursestable.xar_name AS name,
    $coursestable.xar_courseid AS courseid
    FROM $coursestable,
    $courses_studentstable
    WHERE $courses_studentstable.xar_uid = $uid
    AND $coursestable.xar_courseid = $courses_studentstable.xar_course";
    $result = $dbconn->Execute($sql);

    if (!$result) {
    return;
    }
    // if no record found, return an empty array
    if ($result->EOF) {
    return array();
    }

   while(!$result->EOF) {
    $row = $result->GetRowAssoc(false);

    $courses[$row['courseid']] = $row['name'];
    $result->MoveNext();
    }

    // return the items
    return $courses;
    // TODO: how to select by cat ids (automatically) when needed ???
    // Get items - the formatting here is not mandatory, but it does make the
    // SQL statement relatively easy to read.  Also, separating out the sql
    // statement from the SelectLimit() command allows for simpler debug
    // operation if it is ever needed

    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with

}
?>

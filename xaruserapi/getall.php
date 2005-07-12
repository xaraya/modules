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
 * get all courses
 * 
 * @author the Courses module development team 
 * @param numitems $ the number of items to retrieve (default -1 = all)
 * @param startnum $ start with this item number (default 1)
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function courses_userapi_getall($args)
{
    extract($args);
    if (!xarVarFetch('startnum', 'int:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('numitems', 'int:1:', $numitems, '1', XARVAR_NOT_REQUIRED)) return;

    $items = array();
    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
    if (!xarSecurityCheck('ViewCourses')) return;
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // It's good practice to name the table definitions you are
    // using - $table doesn't cut it in more complex modules
    $coursestable = $xartable['courses'];
    // TODO: how to select by cat ids (automatically) when needed ???
    
    // Set to be able to see all courses or only non-hidden ones
    if (xarSecurityCheck('AdminCourses', 0)) {
    $where = "0, 1";
    } else {
    $where = "0";
    }
    // Get items
    $query = "SELECT xar_courseid,
                   xar_name,
                   xar_number,
                   xar_type,
                   xar_level,
                   xar_shortdesc,
                   xar_language,
                   xar_freq,
                   xar_contact,
                   xar_hidecourse,
                   xar_last_modified
            FROM $coursestable
            WHERE xar_hidecourse in ($where)
            ORDER BY xar_number";
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Put items into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($courseid, $name, $number, $coursetype, $level, $shortdesc, $language, $freq, $contact, $hidecourse, $last_modified) = $result->fields;
        if (xarSecurityCheck('ViewCourses', 0, 'Course', "$name:All:$courseid")) {
            $items[] = array('courseid' => $courseid,
                'name' => $name,
                'number' => $number,
                'coursetype' => $coursetype,
                'level' => $level,
                'shortdesc' => $shortdesc,
                'language' => $language,
                'freq' => $freq,
                'contact' => $contact,
                'hidecourse' => $hidecourse,
                'last_modified' => $last_modified);
        }
    }
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();
    // Return the items
    return $items;
}

?>

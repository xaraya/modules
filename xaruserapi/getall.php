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
            join(', ', $invalid), 'user', 'getall', 'courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

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
                   xar_hidecourse
            FROM $coursestable
            WHERE xar_hidecourse in ($where)
            ORDER BY xar_number";
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Put items into result array.  Note that each item is checked
    // individually to ensure that the user is allowed *at least* OVERVIEW
    // access to it before it is added to the results array.
    // If more severe restrictions apply, e.g. for READ access to display
    // the details of the item, this *must* be verified by your function.
    for (; !$result->EOF; $result->MoveNext()) {
        list($courseid, $name, $number, $coursetype, $level, $shortdesc, $language, $freq, $contact, $hidecourse) = $result->fields;
        if (xarSecurityCheck('ViewCourses', 0, 'Item', "$name:All:$courseid")) {
            $items[] = array('courseid' => $courseid,
                'name' => $name,
                'number' => $number,
                'coursetype' => $coursetype,
                'level' => $level,
                'shortdesc' => $shortdesc,
                'language' => $language,
                'freq' => $freq,
                'contact' => $contact,
                'hidecourse' => $hidecourse);
        }
    }
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();
    // Return the items
    return $items;
}

?>

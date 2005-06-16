<?php
/**
 * File: $Id:
 * 
 * Get a specific item
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
 * get a specific item
 * 
 * @author the Courses module development team 
 * @param  $args ['courseid'] id of course item to get
 * @returns array
 * @return item array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function courses_userapi_getcourse($args)
{
    // Get arguments from argument array - all arguments to this function
    // should be obtained from the $args array, getting them from other places
    // such as the environment is not allowed, as that makes assumptions that
    // will not hold in future versions of Xaraya
    extract($args);
    // Argument check - make sure that all required arguments are present and
    // in the right format, if not then set an appropriate error message
    // and return
    if (!isset($courseid) || !is_numeric($courseid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'getcourse', 'courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    // Get database setup - note that both xarDBGetConn() and xarDBGetTables()
    // return arrays but we handle them differently.  For xarDBGetConn() we
    // currently just want the first item, which is the official database
    // handle.  For xarDBGetTables() we want to keep the entire tables array
    // together for easy reference later on
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // It's good practice to name the table and column definitions you are
    // getting - $table and $column don't cut it in more complex modules
    $coursestable = $xartable['courses'];
	
	
    // Get item - the formatting here is not mandatory, but it does make the
    // SQL statement relatively easy to read.  Also, separating out the sql
    // statement from the Execute() command allows for simpler debug operation
    // if it is ever needed
    $query = "SELECT xar_name,
                   xar_number,
                   xar_type,
                   xar_level,
                   xar_shortdesc,
				   xar_language,
				   xar_freq,
				   xar_contact,
				   xar_hidecourse
            FROM $coursestable
            WHERE xar_courseid = ?";
    $result = &$dbconn->Execute($query, array((int)$courseid));
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Check for no rows found, and if so, close the result set and return an exception
    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This course does not exists');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }
	
    // Obtain the item information from the result set
    list($name, $number, $coursetype, $level, $shortdesc, $language, $freq, $contact, $hidecourse) = $result->fields;
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();
    // Security checks 
	
	// Check that person has admin right to see hidden course
    if (!xarSecurityCheck('AdminCourses')) {
		if ($hidecourse == 1){
		return;
		}
	}
    // For this function, the user must *at least* have READ access to this item
    if (!xarSecurityCheck('ReadCourses', 1, 'Item', "$name:All:$courseid")) {
        return;
    }
    // Create the item array
    //$dateformat = '%Y-%m-%d %H:%M:%S';
    //$startdate = xarLocaleFormatDate($dateformat, $startdate);
    //$enddate = xarLocaleFormatDate($dateformat, $enddate);
    $item = array('courseid' => $courseid,
        'name' => $name,
        'number' => $number,
        'coursetype' => $coursetype,
        'level' => $level,
        'shortdesc' => $shortdesc,
        'language' => $language,
		'freq' => $freq,
		'contact' => $contact,
		'hidecourse' => $hidecourse);
    // Return the item array
    return $item;
}

?>

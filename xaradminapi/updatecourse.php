<?php
/**
 * File: $Id:
 * 
 * Update an example item
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
 * update a course
 * 
 * @author the Course module development team 
 * @param  $args ['courseid'] the ID of the course
 * @param  $args ['name'] the new name of the item
 * @param  $args ['number'] the new number of the item
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function courses_adminapi_updatecourse($args)
{
    // Get arguments from argument array - all arguments to this function
    // should be obtained from the $args array, getting them from other
    // places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
    extract($args);
    // Argument check - make sure that all required arguments are present
    // and in the right format, if not then set an appropriate error
    // message and return
    // Note : since we have several arguments we want to check here, we'll
    // report all those that are invalid at the same time...
    $invalid = array();
    if (!isset($courseid) || !is_numeric($courseid)) {
        $invalid[] = 'Course ID';
    }
    if (!isset($name) || !is_string($name)) {
        $invalid[] = 'Course name';
    }
    if (!isset($number) || !is_string($number)) {
        $invalid[] = 'Course number';
    }
/*
     if (empty($hours) || !is_numeric($hours)) {
        $invalid['hours'] = 1;
        $hours = '';
    }

     if (empty($ceu) || !is_numeric($ceu)) {
        $invalid['ceu'] = 1;
        $ceu = '';
    }

     if (empty($startdate) || !is_string($startdate)) {
        $invalid['startdate'] = 1;
        $startdate = '';
    }

     if (empty($enddate) || !is_string($enddate)) {
        $invalid['enddate'] = 1;
        $enddate = '';
    }
*/
    
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'adminapi', 'updatecourse', 'Courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    // The user API function is called.  This takes the item ID which
    // we obtained from the input and gets us the information on the
    // appropriate item.  If the item does not exist we post an appropriate
    // message and return
    $item = xarModAPIFunc('courses',
        'user',
        'getcourse',
        array('courseid' => $courseid));
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing.
    // However, in this case we had to wait until we could obtain the item
    // name to complete the instance information so this is the first
    // chance we get to do the check
    // Note that at this stage we have two sets of item information, the
    // pre-modification and the post-modification.  We need to check against
    // both of these to ensure that whoever is doing the modification has
    // suitable permissions to edit the item otherwise people can potentially
    // edit areas to which they do not have suitable access
    if (!xarSecurityCheck('EditCourses', 1, 'Item', "$item[name]:All:$courseid")) {
        return;
    }
    // Get database setup - note that both xarDBGetConn() and xarDBGetTables()
    // return arrays but we handle them differently.  For xarDBGetConn()
    // we currently just want the first item, which is the official
    // database handle.  For xarDBGetTables() we want to keep the entire
    // tables array together for easy reference later on
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // It's good practice to name the table and column definitions you
    // are getting - $table and $column don't cut it in more complex
    // modules
    $coursestable = $xartable['courses'];
    // Update the item - the formatting here is not mandatory, but it does
    // make the SQL statement relatively easy to read.  Also, separating
    // out the sql statement from the Execute() command allows for simpler
    // debug operation if it is ever needed
    $query = "UPDATE $coursestable
              SET xar_name = ?,
                xar_number = ?,
                xar_type = ?,
                xar_level = ?,
                xar_shortdesc = ?,
                xar_language = ?,
                xar_freq = ?,
                xar_contact = ?,
                xar_hidecourse = ?
              WHERE xar_courseid = ?";

    $bindvars = array($name, $number, $coursetype, $level, $shortdesc, $language, $freq, $contact, $hidecourse, $courseid);
    $result = &$dbconn->Execute($query, $bindvars);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Let any hooks know that we have updated an item.  As this is an
    // update hook we're passing the updated $item array as the extra info
    $item['module'] = 'courses';
    $item['itemid'] = $courseid;
    $item['name'] = $name;
    $item['number'] = $number;
    xarModCallHooks('item', 'update', $courseid, $item);
    // Let the calling process know that we have finished successfully
    return true;
}

?>

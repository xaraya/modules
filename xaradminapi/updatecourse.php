<?php
/**
 * Update a course
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
 * update a course
 *
 * @author MichelV <michelv@xaraya.com>
 * @param  int $args ['courseid'] the ID of the course
 * @param  $args ['name'] the new name of the item
 * @param  int $args ['number'] the code for the course
 * @param  $args all other course variables ;)
 * @return bool
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function courses_adminapi_updatecourse($args)
{
    extract($args);

    // Argument check
    // TODO: should these be in other place? Non-API?
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

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'adminapi', 'updatecourse', 'Courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    // The user API function is called.
    $item = xarModAPIFunc('courses',
        'user',
        'get',
        array('courseid' => $courseid));
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Security check
    if (!xarSecurityCheck('EditCourses', 1, 'Course', "$courseid:All:All")) {
        echo "here";
    }

    $last_modified = time();
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $coursestable = $xartable['courses'];
    // Update the item
    $query = "UPDATE $coursestable
              SET xar_name = ?,
                 xar_number = ?,
                 xar_type = ?,
                 xar_level = ?,
                 xar_shortdesc = ?,
                 xar_intendedcredits = ?,
                 xar_freq = ?,
                 xar_contact = ?,
                 xar_contactuid =?,
                 xar_hidecourse = ?,
                 xar_last_modified = ?
              WHERE xar_courseid = ?";

    $bindvars = array($name, $number, $coursetype, $level, $shortdesc, $intendedcredits, $freq, $contact, $contactuid,
                      $hidecourse, $last_modified, $courseid);
    $result = &$dbconn->Execute($query, $bindvars);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) {
        return false;
    }

    // Let any hooks know that we have updated an item.
    $item = $args;
    $item['module'] = 'courses';
    $item['itemid'] = $courseid;
    $item['itemtype'] = $coursetype;

    xarModCallHooks('item', 'update', $courseid, $item);
    // Let the calling process know that we have finished successfully
    return true;
}

?>

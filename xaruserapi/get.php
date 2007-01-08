<?php
/**
 * Get a specific course
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * get a specific course
 *
 * @author the Courses module development team
 * @param  id courseid id of course item to get
 * @return array with item, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function courses_userapi_get($args)
{
    extract($args);

    if (!isset($courseid) || !is_numeric($courseid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'get', 'courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    /* Get database setup */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $coursestable = $xartable['courses'];
    /* Get the course */
    $query = "SELECT xar_name,
                   xar_number,
                   xar_type,
                   xar_level,
                   xar_shortdesc,
                   xar_intendedcredits,
                   xar_freq,
                   xar_contact,
                   xar_contactuid,
                   xar_hidecourse,
                   xar_last_modified
            FROM $coursestable
            WHERE xar_courseid = ?";// AND xar_hidecourse in ($where)";
    $result = &$dbconn->Execute($query, array($courseid));
    if (!$result) return;
    // Check for no rows found, and if so, close the result set and return an exception
    // TODO: allow for empty result when the user is not allowed to get this course ($where)
    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This course does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }
    // Extract fields
    list($name, $number, $coursetype, $level, $shortdesc, $intendedcredits, $freq, $contact, $contactuid, $hidecourse, $last_modified) = $result->fields;
    $result->Close();

    // Security checks

    // For this function, the user must *at least* have READ access to this item
    if (!xarSecurityCheck('ViewCourses', 1, 'Course', "$courseid:All:All")) {
        return;
        }
    /* TODO: this causes an error at the account function of roles. Why does that call this function?
    //Check if user can see this course
    if ($hidecourse == 1) {
        if(!xarSecurityCheck('AdminCourses', 0, 'Course', "$courseid:All:All")) {
            $msg = xarModGetVar('courses','hidecoursemsg');
            xarErrorSet(XAR_USER_EXCEPTION, 'CANNOT_CONTINUE',
                new DefaultUserException($msg));
            return;
        }
    }
    */
    $item = array('courseid'    => $courseid,
                'name'          => $name,
                'number'        => $number,
                'coursetype'    => $coursetype,
                'level'         => $level,
                'shortdesc'     => $shortdesc,
                'intendedcredits' => $intendedcredits,
                'freq'          => $freq,
                'contact'       => $contact,
                'contactuid'    => $contactuid,
                'hidecourse'    => $hidecourse,
                'last_modified' => $last_modified);
    // Return the item array
    return $item;
}
?>

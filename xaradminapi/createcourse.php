<?php
/**
 * Create a new Course
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
 * create a new course
 *
 * @author the Courses module development team
 * @param  $args ['name'] name of the course
 * @param  $args ['number'] number of the course
 * @returns int
 * @return course ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function courses_adminapi_createcourse($args)
{
    extract($args);
    // Invalid check
    $invalid = array();
    if (!isset($name) || !is_string($name)) {
        $invalid[] = 'name';
    }
    if (!isset($number) || !is_string($number)) {
        $invalid[] = 'number';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'adminapi', 'createcourse', 'Courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AddCourses', 1, 'Course', "All:All:All")) {
        return;
    }

    $last_modified = time();
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $coursestable = $xartable['courses'];
    $nextId = $dbconn->GenId($coursestable);
    // Add item
    $query = "INSERT INTO $coursestable (
              xar_courseid,
              xar_name,
              xar_number,
              xar_type,
              xar_level,
              xar_shortdesc,
              xar_intendedcredits,
              xar_freq,
              xar_contact,
              xar_contactuid,
              xar_hidecourse,
              xar_last_modified)
              VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";

    $bindvars = array($nextId, $name, $number, $coursetype, $level, $shortdesc, $intendedcredits, $freq, $contact, $contactuid, $hidecourse, $last_modified);
    $result = &$dbconn->Execute($query, $bindvars);
    if (!$result) return;

    // Get the ID of the item that we inserted.
    $courseid = $dbconn->PO_Insert_ID($coursestable, 'xar_courseid');

    // Let any hooks know that we have created a new item.
    $item = $args;
    $item['module'] = 'courses';
    $item['itemtype'] = $coursetype;
    $item['itemid'] = $courseid;
    xarModCallHooks('item', 'create', $courseid, $item);
    // Return the id of the newly created item to the calling process
    return $courseid;
}

?>

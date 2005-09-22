<?php
/**
 * File: $Id:
 *
 * Create a new example item
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage courses
 * @author Courses module development team
 */
/**
 * add a teacher to planned course: create a teacher
 *
 * @author the Courses module development team
 * @param  $args ['userid'] uid of teacher
 * @param  $args ['planningid'] number of the planned course
 * @param  $args ['type'] additional info on teacher: type
 * @returns int
 * @return teacher ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function courses_adminapi_create_teacher($args)
{
    extract($args);
  if (!xarVarFetch('planningid', 'int:1:', $planningid, NULL, XARVAR_DONT_SET)) return;
  if (!xarVarFetch('userid', 'int:1:', $userid)) return;
  if (!xarVarFetch('type', 'int:1:', $type, '1', XARVAR_DONT_SET)) return;

    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
    if (!xarSecurityCheck('EditCourses', 1, 'Course', "All:$planningid:All")) {
        return;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $teacherstable = $xartable['courses_teachers'];
    // Get next ID in table
    $nextId = $dbconn->GenId($teacherstable);
    // Add item
    $query = "INSERT INTO $teacherstable (
              xar_tid,
              xar_userid,
              xar_planningid,
              xar_type)
            VALUES (?,?,?,?)";
    $bindvars = array((int)$nextId, (int)$userid, (int)$planningid, $type);
    $result = &$dbconn->Execute($query, $bindvars);
    if (!$result) return;

    // Get the ID of the item that we inserted.
    $tid = $dbconn->PO_Insert_ID($teacherstable, 'xar_tid');

    // TODO: evaluate
    // xarModCallHooks('item', 'create', $exid, 'exid');
    $item = $args;
    $item['module'] = 'courses';
    $item['itemid'] = $tid;
    xarModCallHooks('item', 'create', $tid, $item);
    // Return the id of the newly created item to the calling process
    return $tid;
}

?>

<?php
/**
 * File: $Id:
 *
 * Create a new example item
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
 * update the enrollement for a student
 *
 * @author the Courses module development team
 * @param  $args ['sid'] id of student/participant
 * @param  $args ['planningid'] number of the planned course
 * @param  $args ['statusid'] status of the student enrolling
 * @returns int
 * @return enroll ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function courses_adminapi_updateparticipant($args)
{
    extract($args);
    if (!xarVarFetch('planningid', 'int:1:', $planningid)) return;
    if (!xarVarFetch('sid', 'int:1:', $sid)) return;
    if (!xarVarFetch('statusid', 'int:1:', $statusid)) return;

    $invalid = array();
     if (!isset($sid) || !is_numeric($sid)) {
        $invalid[] = 'sid';
    }
     if (!isset($planningid) || !is_numeric($planningid)) {
        $invalid[] = 'planningid';
    }
     if (!isset($statusid) || !is_numeric($statusid)) {
        $invalid[] = 'statusid';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'create_enroll', 'Courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
    if (!xarSecurityCheck('EditPlanning', 1, 'Planning', "$planningid:All:All")) { //Correct?
        return;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $studentstable = $xartable['courses_students'];
    // Add item
    $query = "UPDATE $studentstable 
              SET xar_planningid = ?,
                  xar_status = ?
              WHERE xar_sid = ?
              ";
    $bindvars = array((int)$planningid, $statusid, $sid);
    $result = &$dbconn->Execute($query, $bindvars);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Let any hooks know that we have created a new item.  As this is a
    // create hook we're passing 'exid' as the extra info, which is the
    // argument that all of the other functions use to reference this
    // item
    // TODO: evaluate
    // xarModCallHooks('item', 'create', $exid, 'exid');
    $item = $args;
    $item['module'] = 'courses';
    $item['itemid'] = $sid;
    xarModCallHooks('item', 'update', $sid, $item);
    // Return the id of the newly created item to the calling process
    return $sid;
}

?>

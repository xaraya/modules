<?php
/**
 * Enroll into a course
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
 * create an enrollment for a student
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param  int $args ['uid'] uid of student
 * @param  int $args ['planningid'] number of the planned course
 * @param  int $args ['studstatus'] status of the student enrolling
 * @return int enroll ID on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function courses_userapi_create_enroll($args)
{
    extract($args);
    if (!xarVarFetch('planningid', 'id',       $planningid)) return;
    if (!xarVarFetch('uid',        'int:1:',   $uid)) return;
    if (!xarVarFetch('studstatus', 'int:1:',   $studstatus, 1)) return;
   // if (!xarVarFetch('regdate',    'int:1:11', $regdate)) return;

    $invalid = array();
     if (!isset($uid) || !is_numeric($uid)) {
        $invalid[] = 'uid';
    }
     if (!isset($planningid) || !is_numeric($planningid)) {
        $invalid[] = 'planningid';
    }
     if (!isset($studstatus) || !is_numeric($studstatus)) {
        $invalid[] = 'studstatus';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'create_enroll', 'Courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    // Security check
    if (!xarSecurityCheck('ReadCourses', 1, 'Course', "All:$planningid:All")) {
        return;
    }

    $regdate = time();
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $studentstable = $xartable['courses_students'];
    // Get next ID in table
    $nextId = $dbconn->GenId($studentstable);
    // Add item
    $query = "INSERT INTO $studentstable (
              xar_sid,
              xar_userid,
              xar_planningid,
              xar_status,
              xar_regdate)
            VALUES (?,?,?,?,?)";
    $bindvars = array($nextId, (int)$uid, (int)$planningid, $studstatus, $regdate);
    $result = &$dbconn->Execute($query, $bindvars);
    if (!$result) return;
    // Get the ID of the item that we inserted.
    $enrollid = $dbconn->PO_Insert_ID($studentstable, 'xar_sid');

    // Return the id of the newly created item to the calling process
    return $enrollid;
}

?>

<?php
/**
 * Check to see if the user can be matched to be a course contact
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author MichelV
 */
/**
 * Check for the case that the user (entered with a userid) is already
    registered as a contact for the course
 * @author MichelV <michelv@xarayahosting.nl>
 * @since 17 Nov 2006
 * @param int courseid The id of the course to check OPTIONAL OR
 * @param int planningid The id of the planned course, to trace the courseid from
 * @param int userid id of the user placed in as a teacher; defaults to current user
 * @return bool true if the combination is encountered, false if not.
 * @todo use a privilege check in here?
 */
function courses_adminapi_check_contact($args)
{
    extract($args);
    if (!xarVarFetch('courseid',   'id',     $courseid, 0,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('planningid', 'id',     $planningid, 0,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('userid',     'int::', $userid, xarUserGetVar('uid'), XARVAR_NOT_REQUIRED)) return;

    $items = array();
    // if (!xarSecurityCheck('EditPlanning')) return;

    if ($courseid == 0 && $planningid == 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'course and planning ID', 'admin', 'check_contact', 'courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    if ($courseid == 0 && $planningid > 0) {
        $course = xarModAPIFunc('courses','user','getplanned',array ('planningid' => $planningid ));
        $courseid = $course['courseid'];
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $coursestable = $xartable['courses'];

    $sql = "SELECT xar_number
    FROM $coursestable
    WHERE xar_contactuid = $userid
    AND xar_courseid = $courseid";
    $result = $dbconn->Execute($sql);
    // check for a result
    if (!$result) {
        return false;
    }
    $ids = array();
    // Get the courseid
    for (; !$result->EOF; $result->MoveNext()) {
        list($number) = $result->fields;
        $ids[] = $number;
    }
    $result->Close();
    if (count($ids) == 0) {
        return false;
    } else {
        // this user is a coursecontact: return true
        return true;
    }
}
?>

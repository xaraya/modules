<?php
/**
 * Check teacher
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
    registered as a teacher for the planned course
 * @author MichelV <michelv@xarayahosting.nl>
 * @param int planningid
 * @param int userid id of the user placed in as a teacher
 * @return bool true if the combination is encountered, false if not.
 * @todo use a privilege check in here?
 */
function courses_adminapi_check_teacher($args)
{
    extract($args);
    if (!xarVarFetch('planningid', 'id', $planningid)) return;
    if (!xarVarFetch('userid', 'int:1:', $userid)) return;

    $items = array();
    // if (!xarSecurityCheck('EditPlanning')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $teacherstable = $xartable['courses_teachers'];

    $sql = "SELECT xar_tid
    FROM $teacherstable
    WHERE xar_userid = $userid
    AND xar_planningid = $planningid";
    $result = $dbconn->Execute($sql);
    // check for a result
    if (!$result) {
        return false;
    }
    $tids = array();
    // Get the courseid
    for (; !$result->EOF; $result->MoveNext()) {
        list($tid) = $result->fields;
        $tids[] = $tid;
    }
    $result->Close();
    if (count($tids) == 0) {
        return false;
    } else {
        // this user is a teacher: return true
        return true;
    }
}
?>

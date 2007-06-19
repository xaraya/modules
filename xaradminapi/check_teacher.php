<?php
/**
 * Check teacher
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
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
 * @param int planningid The id of the planned course
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

    $sql = "SELECT COUNT(xar_tid)
    FROM $teacherstable
    WHERE xar_userid = $userid
    AND xar_planningid = $planningid";
    $result = $dbconn->Execute($sql);
    // check for a result
    if (!$result) {
        // No result: return false
        echo false;
    }
    list($tid) = $result->fields;
    
    $result->Close();
    // Create result
    if ($tid > 0) {
        return true;
    } else {
        // this user is not a teacher: return false
        return false;
    }
}
?>

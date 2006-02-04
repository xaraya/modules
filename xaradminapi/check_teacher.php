<?php
/**
 * Check teacher
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author MichelV
 */
/**
 * see if there is already a link between the current user and a planned course
 * @author MichelV <michelv@xarayahosting.nl>
 * @param planningid
 * @param userid id of the user placed in as a teacher
 * @return array items of found courses
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

    $sql = "SELECT xar_userid, xar_planningid
    FROM $teacherstable
    WHERE xar_userid = $userid
    AND xar_planningid = $planningid";
    $result = $dbconn->Execute($sql);
    // Nothing found: return empty
    $items=array();

    if (!$result) {
        return;
    } else {
        for (; !$result->EOF; $result->MoveNext()) {
            list($userid, $planningid) = $result->fields;
            if (xarSecurityCheck('ViewCourses', 0, 'Course', "All:$planningid:All")) {
                $items[] = array('userid' => $userid,
                                 'planningid' => $planningid);
            }
    }
    $result->Close();
    return $items;
    }
    // TODO: how to select by cat ids (automatically) when needed ???

}
?>

<?php
/**
 * Get all participants for one planned course
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * get all participants for a planned course
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param id planningid The id of the planned course to get the students for
 * @param int numitems $ the number of items to retrieve (default -1 = all)
 * @param int startnum $ start with this item number (default 1)
 * @return array Array of students for one planned course, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function courses_adminapi_getallparticipants($args)
{
    extract($args);
    // Optional arguments.
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }
    // Argument check
    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (!isset($planningid) || !is_numeric($planningid)) {
        $invalid[] = 'planningid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'getallparticipants', 'courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $items = array();
//    if (!xarSecurityCheck('EditCourses', '1', 'Course', "All:$planningid:All")) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $studentstable = $xartable['courses_students'];

    // Get items
    $query = "SELECT xar_sid,
                     xar_userid,
                     xar_planningid,
                     xar_status,
                     xar_regdate
              FROM $studentstable
              WHERE xar_planningid = $planningid
              ORDER BY xar_sid";
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Put items into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($sid, $userid, $planningid, $status, $regdate) = $result->fields;
  //      if (xarSecurityCheck('EditCourses', 0, 'Course', "All:$planningid:All")) {
            $items[] = array('sid'        => $sid,
                             'userid'     => $userid,
                             'planningid' => $planningid,
                             'status'     => $status,
                             'regdate'    => $regdate);
  //      }
    }
    // Close result
    $result->Close();
    // Return the items
    return $items;
}

?>

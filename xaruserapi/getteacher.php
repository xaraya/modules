<?php
/**
 * Get a specific teacher
 *
 * @package modules
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * get a teacher of a planned course
 *
 * @author the Courses module development team
 * @param tid $ the ID of the teacher to get
 * @return array of items, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function courses_userapi_getteacher($args)
{
    extract($args);

    $invalid = array();
    if (!isset($tid) || !is_numeric($tid)) {
        $invalid[] = 'tid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'getteacher', 'courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $item = array();
    if (!xarSecurityCheck('ReadCourses')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $teacherstable = $xartable['courses_teachers'];
    $query = "SELECT xar_tid,
                     xar_userid,
                     xar_planningid,
                     xar_type
              FROM $teacherstable
              WHERE xar_tid = ?";

    $result = $dbconn->Execute($query, array((int)$tid));

    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This teacher does not exists');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }
    // Put item into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($sid, $userid, $planningid, $type) = $result->fields;
        if (xarSecurityCheck('ReadCourses', 0, 'Course', "All:$planningid:All")) { //TODO: check this privilege
            $item = array('tid' 	   => $tid,
                          'userid'     => $userid,
                          'planningid' => $planningid,
                          'type'       => $type);
        }
    }

    $result->Close();
    // Return the items
    return $item;
}

?>

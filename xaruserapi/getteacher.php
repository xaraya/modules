<?php
/**
 * File: $Id:
 * 
 * Get all module items
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
 * get a teacher of a planned course
 * 
 * @author the Courses module development team 
 * @param numitems $ the number of items to retrieve (default -1 = all)
 * @param startnum $ start with this item number (default 1)
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function courses_userapi_getteacher($args)
{
    extract($args);
    // Optional arguments.
    // Note : since we have several arguments we want to check here, we'll
    // report all those that are invalid at the same time...
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
    if (!xarSecurityCheck('ViewPlanning')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $teacherstable = $xartable['courses_teachers'];
    // TODO: how to select by cat ids (automatically) when needed ???
    // Get items - the formatting here is not mandatory, but it does make the
    // SQL statement relatively easy to read.  Also, separating out the sql
    // statement from the SelectLimit() command allows for simpler debug
    // operation if it is ever needed
    $query = "SELECT xar_tid,
                   xar_userid,
                   xar_planningid,
				   xar_type
            FROM $teacherstable
			WHERE xar_tid = ?";
			
    $result = $dbconn->Execute($query, array((int)$tid));
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
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
        if (xarSecurityCheck('ViewPlanning', 0, 'Planning', "$planningid:All:All")) { //TODO
            $item = array('tid' => $tid,
                'userid'        => $userid,
                'planningid'    => $planningid,
				'type'          => $type);
        }
    }
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();
    // Return the items
    return $item;
}

?>

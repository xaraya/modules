<?php
/**
 * File: $Id:
 * 
 * Delete an student item
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage courses
 * @author MichelV. 
 */
/**
 * delete a teacher
 * 
 * @author the courses module development team 
 * @param  $args ['tid'] ID of the teacher item
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function courses_adminapi_deleteteacher($args)
{
    extract($args);
	if (!xarVarFetch('tid', 'int:1:', $tid)) return;

    // Argument check - make sure that all required arguments are present and
    // in the right format, if not then set an appropriate error message
    // and return
    if (!isset($tid) || !is_numeric($tid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'admin', 'deleteteacher', 'Courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    // get item
    $item = xarModAPIFunc('courses',
        'user',
        'getteacher',
        array('tid' => $tid));
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('AdminPlanning', 1, 'Planning', "All:All:All")) { //TODO rewrite
        return;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // It's good practice to name the table and column definitions you
    // are getting - $table and $column don't cut it in more complex
    // modules
    $teacherstable = $xartable['courses_teachers'];
    $query = "DELETE FROM $teacherstable
            WHERE xar_tid = ?";
    $result = &$dbconn->Execute($query, array((int)$tid));
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Let any hooks know that we have deleted an item.  As this is a
    // delete hook we're not passing any extra info
    // xarModCallHooks('item', 'delete', $exid, '');
    $item['module'] = 'courses';
    $item['itemid'] = $tid;
    xarModCallHooks('item', 'delete', $tid, $item);
    // Let the calling process know that we have finished successfully
    return true;
}

?>

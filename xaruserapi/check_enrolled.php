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
 * @author XarayaGeek
 */
/**
 * see if there is already a link between the current user and a planned course
 *
 */
function courses_userapi_check_enrolled($args)
{
    // Get arguments from argument array - all arguments to this function
    // should be obtained from the $args array, getting them from other places
    // such as the environment is not allowed, as that makes assumptions that
    // will not hold in future versions of Xaraya
    extract($args);
    if (!xarVarFetch('planningid', 'int:1:', $planningid)) return;
    if (!xarVarFetch('uid', 'int:1:', $uid)) return;
	
    if (!isset($planningid) || !is_numeric($planningid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'check_enrolled', 'courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $items = array();
    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
    if (!xarSecurityCheck('ViewPlanning')) return;
    // Get database setup - note that both xarDBGetConn() and xarDBGetTables()
    // return arrays but we handle them differently.  For xarDBGetConn() we
    // currently just want the first item, which is the official database
    // handle.  For xarDBGetTables() we want to keep the entire tables array
    // together for easy reference later on
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // It's good practice to name the table definitions you are
    // using - $table doesn't cut it in more complex modules
    $courses_studentstable = $xartable['courses_students'];

    $sql = "SELECT xar_userid, xar_planningid
    FROM $courses_studentstable
    WHERE xar_userid = $uid
    AND xar_planningid = $planningid";
    $result = $dbconn->Execute($sql);
    // Nothing found: return empty
    $items=array();
	
	if (!$result) {return;
	}
	else {
    for (; !$result->EOF; $result->MoveNext()) {
        list($userid, $planningid) = $result->fields;
        if (xarSecurityCheck('ViewPlanning', 0, 'Item', "All:All:$planningid")) {
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

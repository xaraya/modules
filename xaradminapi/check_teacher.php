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
 * @author Michel V.
 */
/**
 * see if there is already a link between the current user and a planned course
 *
 */
function courses_adminapi_check_teacher($args)
{

    extract($args);
    if (!xarVarFetch('planningid', 'int:1:', $planningid)) return;
    if (!xarVarFetch('userid', 'int:1:', $userid)) return;
	
    if (!isset($planningid) || !is_numeric($planningid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'check_enrolled', 'courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $items = array();

    if (!xarSecurityCheck('EditPlanning')) return;

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
	
	if (!$result) {return;
	}
	else {
    for (; !$result->EOF; $result->MoveNext()) {
        list($userid, $planningid) = $result->fields;
        if (xarSecurityCheck('EditPlanning', 0, 'Item', "All:All:$planningid")) {
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

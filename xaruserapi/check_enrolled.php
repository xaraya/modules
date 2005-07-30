<?php
/**
 * File: $Id:
 * 
 * Check to see if user is already attached to a planned course
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage courses
 * @author XarayaGeek, Michel V.
 */
/**
 * see if there is already a link between the current user and a planned course
 *
 * @author Michel V.
 * @param planningid The ID of the planned course
 *
 */
function courses_userapi_check_enrolled($args)
{
    // Get arguments from argument array
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
    // Security check
    if (!xarSecurityCheck('ViewPlanning')) return;
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
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
        if (xarSecurityCheck('ViewPlanning', 0, 'Planning', "All:All:$planningid")) {
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

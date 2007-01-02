<?php
/**
 * xProject Module - A project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author St.Ego
 */
 
function xproject_adminapi_updatehours($args)
{
    // ALL HOURS_SPENT ARE ASSUMED NEW HOURS, NOT UPDATES TO EXISTING HOURS. MODIFY THE TASK TO UPDATE THE HOURS EXPLICITLY
    extract($args);
    
    if(!isset($hours_spent_delta) || !is_float($hours_spent_delta)) {
        $hours_spent = 0.00;
    }
    
    if(!isset($hours_remaining_delta) || !is_float($hours_remaining_delta)) {
        $hours_remaining = 0.00;
    }
    
    if(!isset($hours_planned_delta) || !is_float($hours_planned_delta)) {
        $hours_planned = 0.00;
    }

    $invalid = array();
    if (!isset($projectid) || !is_numeric($projectid)) {
        $invalid[] = 'Project ID';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'updatehours', 'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $projectinfo = xarModAPIFunc('xproject',
                            'user',
                            'get',
                            array('projectid' => $projectid));

    if (!isset($projectinfo) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('EditXProject', 1, 'Item', "$projectinfo[project_name]:All:$projectid")) {
        return;
    }
    
    // we are adding hours planned/spent, and comparing hours remaining, not comparing hours planned/spent
    $hours_planned = $projectinfo['hours_planned'] + $hours_planned_delta;
    $hours_spent = $projectinfo['hours_spent'] + $hours_spent_delta;
    $hours_remaining = $projectinfo['hours_remaining'] + $hours_remaining_delta;
    
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $xprojecttable = $xartable['xProjects'];

    $query = "UPDATE $xprojecttable
            SET hours_planned = ?,
                hours_spent = ?,
                hours_remaining = ?
            WHERE projectid = ?";

    $bindvars = array(
                    $hours_planned,
                    $hours_spent,
                    $hours_remaining,
                    $projectid);
              
    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;

    return true;
}
?>
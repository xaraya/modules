<?php
/**
 * XTasks Module - A task management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xTasks Module
 * @link http://xaraya.com/index.php/release/704.html
 * @author St.Ego
 */
 
function xtasks_adminapi_updatehours($args)
{
    // ALL HOURS_SPENT ARE ASSUMED NEW HOURS, NOT UPDATES TO EXISTING HOURS. MODIFY THE TASK TO UPDATE THE HOURS EXPLICITLY
    extract($args);
    
    if(!isset($hours_planned_delta)) {
        $hours_planned_delta = 0.00;
    } else {
        $hours_planned_delta = (float)$hours_planned_delta;
    }
    
    if(!isset($hours_spent_delta)) {
        $hours_spent_delta = 0.00;
    } else {
        $hours_spent_delta = (float)$hours_spent_delta;
    }
    
    if(!isset($hours_remaining_delta)) {
        $hours_remaining_delta = 0.00;
    } else {
        $hours_remaining_delta = (float)$hours_remaining_delta;
    }

    $invalid = array();
    if (!isset($taskid) || !is_numeric($taskid)) {
        $invalid[] = 'Task ID';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'updatehours', 'xtasks');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    
    $taskinfo = xarModAPIFunc('xtasks',
                            'user',
                            'get',
                            array('taskid' => $taskid));
    if (!isset($taskinfo) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {
        return; // throw back
    }

    if (!xarSecurityCheck('EditXTask', 1, 'Item', "$taskinfo[task_name]:All:$taskid")) {
        return;
    }
    
    // we are adding hours planned/spent, and comparing hours remaining, not comparing hours planned/spent
    $hours_planned = $taskinfo['hours_planned'] + $hours_planned_delta;
    $hours_spent = $taskinfo['hours_spent'] + $hours_spent_delta;
    $hours_remaining = $taskinfo['hours_remaining'] + $hours_remaining_delta;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $xtasktable = $xartable['xtasks'];

    $query = "UPDATE $xtasktable
            SET hours_planned = ?,
                hours_spent = ?,
                hours_remaining = ?
            WHERE taskid = ?";

    $bindvars = array(
                    $hours_planned,
                    $hours_spent,
                    $hours_remaining,
                    $taskid);
              
    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;
    
    $mymemberid = xarModGetUserVar('xproject', 'mymemberid');
    if(!empty($taskinfo['owner']) && $taskinfo['owner'] != $mymemberid) {
        xarModAPIFunc('xtasks', 'user', 'notify', array('contacttype' => 735, 'owner' => $taskinfo['owner'], 'taskid' => $taskid, 'action' => "HOURS"));
    }
    
    if($taskinfo['parentid'] > 0) {
        xarModAPIFunc('xtasks', 'admin', 'updatehours',
                    array('taskid' => $taskinfo['parentid'],
                        'hours_planned_delta' => $hours_planned_delta,
                        'hours_spent_delta' => $hours_spent_delta,
                        'hours_remaining_delta' => $hours_remaining_delta));
    }
    
    // BEGIN XPROJECT HOURS TIE-IN
    if($taskinfo['projectid'] > 0) {
        $projectinfo = xarModAPIFunc('xproject', 'user', 'get', array('projectid' => $taskinfo['projectid']));
        
        $parent_hours_remaining = $projectinfo['hours_remaining'] + $hours_remaining_delta;

        xarModAPIFunc('xproject', 'admin', 'updatehours',
                    array('projectid' => $taskinfo['projectid'],
                        'hours_planned_delta' => $hours_planned_delta,
                        'hours_spent_delta' => $hours_spent_delta,
                        'hours_remaining_delta' => $hours_remaining_delta));
    }

    return true;
}
?>
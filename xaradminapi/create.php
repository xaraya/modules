<?php
/**
 * Administration System
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage xTasks Module
 * @link http://xaraya.com/index.php/release/704.html
 * @author St.Ego
*/
function xtasks_adminapi_create($args)
{
    extract($args);

    $invalid = array();
    if (!isset($task_name) || !is_string($task_name)) {
        $invalid[] = 'task_name';
    }
    if (isset($projectid) && $projectid == "error") {
        $invalid[] = 'Invalid Project';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'admin', 'create', 'xtasks');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
    
    $xproject_modid = xarModGetIDFromName('xProject');
    if($modid == $xproject_modid) {
        $projectid = $objectid;
    }
    
    // NEED TO TOGGLE BASED ON CONTACT FIELD TYPE
    $mymemberid = xarModGetUserVar('xproject', 'mymemberid'); // xarSessionGetVar('uid'); // 
    
    $timezonestr = xarModGetUserVar('roles','usertimezone');
    if(!empty($timezonestr)) {
        $timezonearray = unserialize($timezonestr);
        $timezone = $timezonearray['timezone'];
    } else {
        $timezone = xarConfigGetVar('Site.Core.TimeZone');
    }
    

    if (!isset($creator) || !is_numeric($creator)) {
        $creator = $mymemberid;
    }

    if (!isset($owner) || !is_numeric($owner)) {
        $owner = $mymemberid;
    }

    if (!isset($assigner) || !is_numeric($assigner)) {
        $assigner = $mymemberid;
    }
    
    if (!xarSecurityCheck('AddXProject', 1, 'Item', "$task_name:All:All")) {
        $msg = xarML('Not authorized to add #(1) items',
                    'xtasks');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $xtaskstable = $xartable['xtasks'];

    $nextId = $dbconn->GenId($xtaskstable);

    $query = "INSERT INTO $xtaskstable (
                  taskid,
                  objectid,
                  modid,
                  itemtype,
                  parentid,
                  dependentid,
                  projectid,
                  task_name,
                  status,
                  priority,
                  importance,
                  description,
                  private,
                  creator,
                  owner,
                  assigner,
                  groupid,
                  date_created,
                  date_approved,
                  date_changed,
                  date_start_planned,
                  date_start_actual,
                  date_end_planned,
                  date_end_actual,
                  hours_planned,
                  hours_spent,
                  hours_remaining)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),NULL,NOW(),?,?,?,?,?,?,?)";
            
    $bindvars = array(
                    $nextId,
                    $objectid ? $objectid : 0,
                    $modid ? $modid : 0,
                    $itemtype ? $itemtype : 0,
                    $parentid ? $parentid : 0,
                    $dependentid ? $dependentid : 0,
                    $projectid ? $projectid : 0,
                    $task_name,
                    $status,
                    $priority ? $priority : 9,
                    $importance,
                    $description,
                    $private ? $private : 0,
                    $creator ? $creator : 0,
                    $owner ? $owner : 0,
                    $assigner ? $assigner : 0,
                    $groupid ? $groupid : 0,
                    $date_start_planned ? $date_start_planned." ".$timezone : NULL,
                    $date_start_actual ? $date_start_actual." ".$timezone : NULL,
                    $date_end_planned ? $date_end_planned." ".$timezone : NULL,
                    $date_end_actual ? $date_end_actual." ".$timezone : NULL,
                    $hours_planned,
                    $hours_spent,
                    $hours_remaining);
    $result = &$dbconn->Execute($query,$bindvars);
    if (!$result) return;

// PRIVATE INITIALLY SET BASED ON USER PREFERENCE


    $taskid = $dbconn->PO_Insert_ID($xtaskstable, 'taskid');
    
    if(!empty($owner) && $owner != $mymemberid) {
        xarModAPIFunc('xtasks', 'user', 'notify', array('contacttype' => 779, 'owner' => $owner, 'taskid' => $taskid, 'action' => "CREATE"));
    }
    
    if($parentid > 0) {
        $parentinfo = xarModAPIFunc('xtasks', 'user', 'get', array('taskid' => $parentid));
        
        $parent_hours_remaining = $parentinfo['hours_remaining'] + $hours_remaining;
        
        xarModAPIFunc('xtasks', 'admin', 'updatehours',
                    array('taskid' => $parentinfo['taskid'],
                        'hours_planned_delta' => $hours_planned,
                        'hours_spent_delta' => $hours_spent,
                        'hours_remaining_delta' => $hours_remaining));
    }
    
    if($projectid > 0) {
        $projectinfo = xarModAPIFunc('xproject', 'user', 'get', array('projectid' => $projectid));
        if($projectinfo == false) return;
        
        if($date_end_planned > $projectinfo['planned_end_date']) {
            $projectinfo['planned_end_date'] = $date_end_planned;
            if(!xarModAPIFunc('xproject', 'admin', 'update',$projectinfo)) return;
        }
    }
        
    $item = $args;
    $item['module'] = 'xtasks';
    $item['itemid'] = $taskid;
    xarModCallHooks('item', 'create', $taskid, $item);

    return $taskid;
}

?>
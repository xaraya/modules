<?php

/**
 * File: $Id$
 *
 * Administrative API functions for tasks module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * 
 * @subpackage tasks
 * @author Chad Kraeft
*/

/**
 * Create a new task
 *
 */
function tasks_adminapi_create($args)
{
    extract($args);

    if (empty($name)) {
        xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>tasks_adminapi_create: ' . _TASKS_MODARGSERROR);
        return false;
    }
	
//     if (!pnSecAuthAction(0, 'tasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_COMMENT)) {
//         pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_create: ' . _TASKS_NOAUTH);
//         return false;
//     }
		
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $taskstable = $xartable['tasks'];

    $nextId = $dbconn->GenId($taskstable);

    $sql = "INSERT INTO $taskstable (
              xar_id,
			   xar_parentid,
			   xar_modname,
			   xar_objectid,
			   xar_name,
			   xar_status,
			   xar_priority,
			   xar_description,
			   xar_private,
			   xar_creator,
			   xar_owner,
			   xar_assigner,
			   xar_date_created,
			   xar_date_approved,
			   xar_date_changed,
			   xar_date_start_planned,
			   xar_date_start_actual,
			   xar_date_end_planned,
			   xar_date_end_actual,
			   xar_hours_planned,
			   xar_hours_spent,
			   xar_hours_remaining)
            VALUES (
              $nextId,
			  " . ($parentid ? $parentid : 0) . ",
			  '" . xarVarPrepForStore($modname) . "',
			  " . ($objectid ? $objectid : 0) . ",
              '" . xarVarPrepForStore($name) . "',
              " . xarVarPrepForStore($status) . ",
              " . xarVarPrepForStore($priority) . ",
              '" . xarVarPrepForStore($description) . "',
              " . ($private ? $private : "0") . ",
              " . xarSessionGetVar('uid') . ",
              " . xarSessionGetVar('uid') . ",
              " . xarSessionGetVar('uid') . ",
              " . time() . ",
			  0,
			  " . time() . ",
			  0,
			  0,
			  0,
			  0,
			  '',
			  '',
			  '')";			  

    // PRIVATE INITIALLY SET BASED ON USER PREFERENCE
    
    $res =& $dbconn->Execute($sql);
    if (!$res) return;

    $id = $dbconn->PO_Insert_ID($taskstable, 'xar_id');

    $returnid = (xarModGetVar('tasks','returnfromedit') ? $id : $parentid);
	return $returnid;
}

function tasks_adminapi_update($args)
{
    extract($args);

    if (!isset($id)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_update: ' . _MODARGSERROR);
        return false;
    }

    if (!pnModAPILoad('tasks', 'user')) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_update: ' . _LOADFAILED);
        return $output->GetOutput();
    }

    $task = pnModAPIFunc('tasks',
						'user',
						'get',
						array('id' => $id));
			
	if ($task == false) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_update: ' . _TASKS_NOSUCHITEM);
        return $output->GetOutput();
    }

    if (!pnSecAuthAction(0, 'tasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_COMMENT)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_update: ' . _TASKS_NOAUTH);
        return false;
    }
			
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    $taskstable = $pntable['tasks'];

    $sql = "UPDATE $taskstable
              SET xar_name = '" . pnVarPrepForStore($name) . "',
				  xar_status = " . pnVarPrepForStore($status) . ",
				  xar_priority = " . pnVarPrepForStore($priority) . ",
				  xar_description = '" . pnVarPrepForStore($description) . "'
			WHERE xar_id = $id";

    $res =& $dbconn->Execute($sql);
    if (!$res) return;

    $returnid = (pnModGetVar('tasks','returnfromedit') ? $id : $task['parentid']);
	return $returnid;
}

function tasks_adminapi_close($args)
{
    extract($args);

    if (!isset($id)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_close: ' . _MODARGSERROR);
        return false;
    }

    if (!pnModAPILoad('tasks', 'user')) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_close: ' . _LOADFAILED);
        return $output->GetOutput();
    }

    $task = pnModAPIFunc('tasks',
						'user',
						'get',
						array('id' => $id));
			
	if ($task == false) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_close: ' . _TASKS_NOSUCHITEM);
        return $output->GetOutput();
    }

    if (!pnSecAuthAction(0, 'tasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_MODERATE)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_close: ' . _TASKS_NOAUTH);
        return false;
    }
			
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    $taskstable = $pntable['tasks'];

    $sql = "UPDATE $taskstable
              SET xar_status = 1,
			  		xar_date_changed = '" . time() . "'
			WHERE xar_id = $id";

    $res =& $dbconn->Execute($sql);
    if (!$res) return;

    $returnid = (pnModGetVar('tasks','returnfrommigrate') ? $id : $task['parentid']);
	return $task['parentid'];
}

function tasks_adminapi_open($args)
{
    extract($args);

    if (!isset($id)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_open: ' . _MODARGSERROR);
        return false;
    }

    if (!pnModAPILoad('tasks', 'user')) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_open: ' . _LOADFAILED);
        return $output->GetOutput();
    }

    $task = pnModAPIFunc('tasks',
						'user',
						'get',
						array('id' => $id));
			
	if ($task == false) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_open: ' . _TASKS_NOSUCHITEM);
        return $output->GetOutput();
    }

    if (!pnSecAuthAction(0, 'tasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_MODERATE)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_open: ' . _TASKS_NOAUTH);
        return false;
    }
			
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    $taskstable = $pntable['tasks'];

    $sql = "UPDATE $taskstable
              SET xar_status = 0,
			  		xar_date_changed = '" . time() . "'
			WHERE xar_id = $id";

    $res =& $dbconn->Execute($sql);
    if (!$res) return;

    $returnid = (pnModGetVar('tasks','returnfrommigrate') ? $id : $task['parentid']);
	return $task['parentid'];
}

function tasks_adminapi_approve($args)
{
    extract($args);

    if (!isset($id)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_approve: ' . _MODARGSERROR);
        return false;
    }

    if (!pnModAPILoad('tasks', 'user')) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_approve: ' . _LOADFAILED);
        return $output->GetOutput();
    }

    $task = pnModAPIFunc('tasks',
						'user',
						'get',
						array('id' => $id));
			
	if ($task == false) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_approve: ' . _TASKS_NOSUCHITEM);
        return $output->GetOutput();
    }

    if (!pnSecAuthAction(0, 'tasks::task', '::$task[basetaskid]', ACCESS_EDIT)
			|| !pnSecAuthAction(0, 'tasks::', "$name::$id", ACCESS_EDIT)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_approve: ' . _TASKS_NOAUTH);
        return false;
    }
			
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    $taskstable = $pntable['tasks'];

    $sql = "UPDATE $taskstable
              SET xar_date_approved = '" . time() . "'
			  		xar_date_changed = '" . time() . "'
			WHERE xar_id = $id";

    $res =& $dbconn->Execute($sql);
    if (!$res) return;

    $returnid = (pnModGetVar('tasks','returnfrommigrate') ? $id : $task['parentid']);
	return $returnid;
}

function tasks_adminapi_publish($args)
{
    extract($args);

    if (!isset($id)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_publish: ' . _MODARGSERROR);
        return false;
    }

    if (!pnModAPILoad('tasks', 'user')) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_publish: ' . _LOADFAILED);
        return $output->GetOutput();
    }

    $task = pnModAPIFunc('tasks',
						'user',
						'get',
						array('id' => $id));
			
	if ($task == false) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_publish: ' . _TASKS_NOSUCHITEM);
        return $output->GetOutput();
    }

    if (!pnSecAuthAction(0, 'tasks::task', '::$task[basetaskid]', ACCESS_EDIT)
			|| !pnSecAuthAction(0, 'tasks::', "$name::$id", ACCESS_EDIT)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_publish: ' . _TASKS_NOAUTH);
        return false;
    }
			
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    $taskstable = $pntable['tasks'];

	$private = $task['private'];

    $sql = "UPDATE $taskstable
              SET xar_private = " . ($task['private'] ? "0" : "1") . ",
			  		xar_date_changed = '" . time() . "'
			WHERE xar_id = $id";

    $res = & $dbconn->Execute($sql);
    if (!$res) return;

    $returnid = (pnModGetVar('tasks','returnfrommigrate') ? $id : $task['parentid']);
	return $returnid;
}

function tasks_adminapi_accept($args)
{
    extract($args);

    if (!isset($id)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_accept: ' . _MODARGSERROR);
        return false;
    }

    if (!pnModAPILoad('tasks', 'user')) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_accept: ' . _LOADFAILED);
        return $output->GetOutput();
    }

    $task = pnModAPIFunc('tasks',
						'user',
						'get',
						array('id' => $id));
			
	if ($task == false) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_accept: ' . _TASKS_NOSUCHITEM);
        return $output->GetOutput();
    }

    if (!pnSecAuthAction(0, 'tasks::task', '::$task[basetaskid]', ACCESS_EDIT)
			|| !pnSecAuthAction(0, 'tasks::', "$name::$id", ACCESS_EDIT)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_accept: ' . _TASKS_NOAUTH);
        return false;
    }
			
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    $taskstable = $pntable['tasks'];

	// NEED CHECK TO ENSURE LOGGED IN
    $sql = "UPDATE $taskstable
              SET xar_owner = " . pnSessionGetVar('uid') . ",
			  		xar_date_changed = '" . time() . "'
			WHERE xar_id = $id";

    $res =& $dbconn->Execute($sql);
    if (!$res) return;

    $returnid = (pnModGetVar('tasks','returnfrommigrate') ? $id : $task['parentid']);
	return $returnid;
}

// DEPRECATED
/*
function tasks_adminapi_delete($args)
{
    extract($args);

    if (!isset($id) || !is_numeric($id)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_delete: ' . _TASKS_MODARGSERROR);
        return false;
    }

    if (!pnModAPILoad('tasks', 'user')) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_delete: ' . _TASKS_LOADFAILED);
        return $output->GetOutput();
    }

    // does it exist ?
    $task = pnModAPIFunc('tasks',
							'user',
							'get',
							array('id' => $id));

    if ($task == false) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_delete: ' . _TASKS_NOSUCHITEM);
        return $output->GetOutput();
    }

    if (!pnSecAuthAction(0, 'tasks::task', '::$task[basetaskid]', ACCESS_DELETE)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_delete: ' . _TASKS_NOAUTH);
        return false;
    }

    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    $taskstable = $pntable['tasks'];

    $sql = "DELETE FROM $taskstable
            WHERE xar_id = " . pnVarPrepForStore($id);
    $result = $dbconn->Execute($sql);
    if (!$result) return;

    $switchboardtable = $pntable['tasks_switchboard'];

    $sql = "DELETE FROM $switchboardtable
            WHERE xar_id = " . pnVarPrepForStore($id);
    $result = $dbconn->Execute($sql);
    if (!$result) return;

    return true;
}
*/
function tasks_adminapi_migrate($args)
{
    extract($args);

	if (!isset($targetfocus)) $targetfocus = 0;

	if (!pnModAPILoad('tasks', 'user')) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_migrate: ' . _TASKS_LOADFAILED);
        return false;
    }

	if(is_array($taskfocus) && count($taskfocus) > 0) {
		foreach($taskfocus as $targetid => $focus) {
			if($focus) $targetfocus = $targetid;
		}
	}
		
	$affectedtasks = array();
	if(is_array($taskcheck) && count($taskcheck) > 0) {
		foreach($taskcheck as $affectedid => $check) {
			if($affectedid != $targetfocus) $affectedtasks[] = $affectedid;
		}
	}
	
	if(count($affectedtasks) <= 0) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_migrate: ' . _TASKS_NOAFFECTEDTASKS);
        return false;
	}
	
// NEED TO ADAPT TO ROOT TASK PERMISSIONS USING GETROOT AND GET
	if(empty($parentid)) {
		$id = ($targetfocus > 0 ? $targetfocus : $affectedid);
	} else $id = $parentid;
// echo "id: $id, parentid: $parentid, targetfocus: $targetfocus, affectedid: $affectedid<br>";
    $parenttask = pnModAPIFunc('tasks',
							'user',
							'get',
							array('id' => $id));
			
	if ($parenttask == false) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_NOSUCHITEM);
        return false;
    }

    if (!pnSecAuthAction(0, 'tasks::task', '::$parenttask[basetaskid]', ACCESS_MODERATE)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_adminapi_migrate: ' . $parenttask['basetaskid'] .  _TASKS_NOAUTH);
        return false;
    }

    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    $taskstable = $pntable['tasks'];
	
	if($targetfocus > 0) {
		// - 1 => Migrate selected tasks under taskfocus (taskfocus[any] = 1)
		$sql = "UPDATE $taskstable SET xar_parentid = " . ($targetfocus ? $targetfocus : "0") . " WHERE xar_id IN (" . implode(",",$affectedtasks) . ")";

		$res =& $dbconn->Execute($sql);
	    if (!$res) return;

		$returnid = (pnModGetVar('tasks','returnfrommigrate') ? $targetfocus : $parentid);
		return $returnid;

	} elseif($taskoption == 1) {
		// - 2 => Surface selected tasks to current task's parentid (taskoption = 1)
		// UH, THERE IS NO PARENTID PASSED
		$sql = "UPDATE $taskstable SET xar_parentid = " . ($parenttask['parentid'] ? $parenttask['parentid'] : "0") . " WHERE xar_id IN (" . implode(",",$affectedtasks) . ")";

		$res =& $dbconn->Execute($sql);
	    if (!$res) return;

		$returnid = (pnModGetVar('tasks','returnfromsurface') ? $parentid : $parenttask['parentid']);
		return $returnid;
	
	} elseif($taskoption == 2) {
		// - 3 => Delete selected tasks and all subtasks (taskoption = 2)
		$resultlist = array();
		$resultlist[] = $affectedtasks;
		$selectedids = $affectedtasks;
		$numtasks = count($affectedtasks);
		while($numtasks > 0) {
			$sql = "SELECT xar_id FROM $taskstable WHERE xar_parentid IN (" . implode(",",$selectedids) . ")";

			$result = $dbconn->SelectLimit($sql, -1, 0);
			if (!$result) return;
			
			$selectedids = array();
			for (; !$result->EOF; $result->MoveNext()) {
				list($selectedid) = $result->fields;
				$selectedids[] = $selectedid;
			}
			$resultlist[] = $selectedids;
			$numtasks = count($selectedids);
		}

		foreach($resultlist as $tasklist) {
			if(is_array($tasklist) && count($tasklist) > 0) {
				$sql = "DELETE FROM $taskstable WHERE xar_id IN (" . implode(",",$tasklist) . ")";
	
				$res =& $dbconn->Execute($sql);
			    if (!$res) return;

			}
		}
		
		return $parentid;
		
	} elseif($taskoption == 3) {
		// - 4 => Delete task, but surface children under current task
		// WHICH SHOULD GO FIRST?
		// ? IF UPDATE FAILS FIRST, ERRMSG AND DO NOT DEL
		// ? IF DEL FAILS FIRST, CONTINUE W/UPDATE
		// IN SECOND SCENARIO, UNSUCCESSFUL UPDATES BECOME ORPHANS
		// HANDLE THAT AS PREVIOUSLY NOTED
		$sql = "UPDATE $taskstable SET xar_parentid = " . ($parentid ? $parentid : "0") . " WHERE xar_parentid IN (" . implode(",",$affectedtasks) . ")";

		$res =& $dbconn->Execute($sql);
        if (!$res) return;

		$sql = "DELETE FROM $taskstable WHERE xar_id IN (" . implode(",",$affectedtasks) . ")";

		$res =& $dbconn->Execute($sql);
        if (!$res) return;

		return $parentid;
		
	} else $sql = "(no query)";
	//
	// EXPECTED ISSUES:
	// * Deletion of subtasks must be recursive/iterative 
	// (resolved by creating an array*array of id lists to use with an "IN" statement recursively)
	// everything else looks pretty cake, yeah?
	//
	///////////////////////////////////

    return $parentid;
}

function tasks_adminapi_getmenulinks() 
{

    $menulinks[] = Array('url'   => xarModURL('tasks',
                                              'admin',
                                              'new'),
                         'label' => xarML('Add Task'),
                         'title' => xarML('Add a new task'));
    $menulinks[] = Array('url'   => xarModURL('tasks',
                                              'user',
                                              'view'),
                         'label' => xarML('View tasks'),
                         'title' => xarML('View registered tasks'));
    $menulinks[] = Array('url'   => xarModURL('tasks',
                                              'admin',
                                              'modifyconfig'),
                         'label' => xarML('Modify config'),
                         'title' => xarML('Modify tasks configuration'));

    return $menulinks;
}
?>
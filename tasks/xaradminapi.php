<?php
function xtasks_adminapi_create($args)
{
    extract($args);

    if (empty($name)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_create: ' . _XTASKS_MODARGSERROR);
        return false;
    }
	
    if (!pnSecAuthAction(0, 'xTasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_COMMENT)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_create: ' . _XTASKS_NOAUTH);
        return false;
    }
		
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    $xtaskstable = $pntable['xtasks'];

    $nextId = $dbconn->GenId($xtaskstable);

    $sql = "INSERT INTO $xtaskstable (
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
			  '" . pnVarPrepForStore($modname) . "',
			  " . ($objectid ? $objectid : 0) . ",
              '" . pnVarPrepForStore($name) . "',
              " . pnVarPrepForStore($status) . ",
              " . pnVarPrepForStore($priority) . ",
              '" . pnVarPrepForStore($description) . "',
              " . ($private ? $private : "0") . ",
              " . pnSessionGetVar('uid') . ",
              " . pnSessionGetVar('uid') . ",
              " . pnSessionGetVar('uid') . ",
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

    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_create: ' . _XTASKS_CREATEFAILED . '<br>' . $sql);
        return false;
    }

    $id = $dbconn->PO_Insert_ID($xtaskstable, 'xar_id');

    $returnid = (pnModGetVar('xtasks','returnfromedit') ? $id : $parentid);
	return $returnid;
}

function xtasks_adminapi_update($args)
{
    extract($args);

    if (!isset($id)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_update: ' . _MODARGSERROR);
        return false;
    }

    if (!pnModAPILoad('xtasks', 'user')) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_update: ' . _LOADFAILED);
        return $output->GetOutput();
    }

    $task = pnModAPIFunc('xtasks',
						'user',
						'get',
						array('id' => $id));
			
	if ($task == false) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_update: ' . _XTASKS_NOSUCHITEM);
        return $output->GetOutput();
    }

    if (!pnSecAuthAction(0, 'xTasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_COMMENT)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_update: ' . _XTASKS_NOAUTH);
        return false;
    }
			
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    $xtaskstable = $pntable['xtasks'];

    $sql = "UPDATE $xtaskstable
              SET xar_name = '" . pnVarPrepForStore($name) . "',
				  xar_status = " . pnVarPrepForStore($status) . ",
				  xar_priority = " . pnVarPrepForStore($priority) . ",
				  xar_description = '" . pnVarPrepForStore($description) . "'
			WHERE xar_id = $id";

    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_update: ' . _XTASKS_UPDATEFAILED . '<br>' . $sql);
        return false;
    }

    $returnid = (pnModGetVar('xtasks','returnfromedit') ? $id : $task['parentid']);
	return $returnid;
}

function xtasks_adminapi_close($args)
{
    extract($args);

    if (!isset($id)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_close: ' . _MODARGSERROR);
        return false;
    }

    if (!pnModAPILoad('xtasks', 'user')) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_close: ' . _LOADFAILED);
        return $output->GetOutput();
    }

    $task = pnModAPIFunc('xtasks',
						'user',
						'get',
						array('id' => $id));
			
	if ($task == false) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_close: ' . _XTASKS_NOSUCHITEM);
        return $output->GetOutput();
    }

    if (!pnSecAuthAction(0, 'xTasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_MODERATE)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_close: ' . _XTASKS_NOAUTH);
        return false;
    }
			
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    $xtaskstable = $pntable['xtasks'];

    $sql = "UPDATE $xtaskstable
              SET xar_status = 1,
			  		xar_date_changed = '" . time() . "'
			WHERE xar_id = $id";

    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_close: ' . _XTASKS_CLOSEFAILED . '<br>' . $sql);
        return false;
    }

    $returnid = (pnModGetVar('xtasks','returnfrommigrate') ? $id : $task['parentid']);
	return $task['parentid'];
}

function xtasks_adminapi_open($args)
{
    extract($args);

    if (!isset($id)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_open: ' . _MODARGSERROR);
        return false;
    }

    if (!pnModAPILoad('xtasks', 'user')) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_open: ' . _LOADFAILED);
        return $output->GetOutput();
    }

    $task = pnModAPIFunc('xtasks',
						'user',
						'get',
						array('id' => $id));
			
	if ($task == false) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_open: ' . _XTASKS_NOSUCHITEM);
        return $output->GetOutput();
    }

    if (!pnSecAuthAction(0, 'xTasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_MODERATE)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_open: ' . _XTASKS_NOAUTH);
        return false;
    }
			
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    $xtaskstable = $pntable['xtasks'];

    $sql = "UPDATE $xtaskstable
              SET xar_status = 0,
			  		xar_date_changed = '" . time() . "'
			WHERE xar_id = $id";

    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_open: ' . _XTASKS_OPENFAILED . '<br>' . $sql);
        return false;
    }

    $returnid = (pnModGetVar('xtasks','returnfrommigrate') ? $id : $task['parentid']);
	return $task['parentid'];
}

function xtasks_adminapi_approve($args)
{
    extract($args);

    if (!isset($id)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_approve: ' . _MODARGSERROR);
        return false;
    }

    if (!pnModAPILoad('xtasks', 'user')) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_approve: ' . _LOADFAILED);
        return $output->GetOutput();
    }

    $task = pnModAPIFunc('xtasks',
						'user',
						'get',
						array('id' => $id));
			
	if ($task == false) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_approve: ' . _XTASKS_NOSUCHITEM);
        return $output->GetOutput();
    }

    if (!pnSecAuthAction(0, 'xTasks::task', '::$task[basetaskid]', ACCESS_EDIT)
			|| !pnSecAuthAction(0, 'xtasks::', "$name::$id", ACCESS_EDIT)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_approve: ' . _XTASKS_NOAUTH);
        return false;
    }
			
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    $xtaskstable = $pntable['xtasks'];

    $sql = "UPDATE $xtaskstable
              SET xar_date_approved = '" . time() . "'
			  		xar_date_changed = '" . time() . "'
			WHERE xar_id = $id";

    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_approve: ' . _XTASKS_APPROVEFAILED . '<br>' . $sql);
        return false;
    }

    $returnid = (pnModGetVar('xtasks','returnfrommigrate') ? $id : $task['parentid']);
	return $returnid;
}

function xtasks_adminapi_publish($args)
{
    extract($args);

    if (!isset($id)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_publish: ' . _MODARGSERROR);
        return false;
    }

    if (!pnModAPILoad('xtasks', 'user')) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_publish: ' . _LOADFAILED);
        return $output->GetOutput();
    }

    $task = pnModAPIFunc('xtasks',
						'user',
						'get',
						array('id' => $id));
			
	if ($task == false) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_publish: ' . _XTASKS_NOSUCHITEM);
        return $output->GetOutput();
    }

    if (!pnSecAuthAction(0, 'xTasks::task', '::$task[basetaskid]', ACCESS_EDIT)
			|| !pnSecAuthAction(0, 'xtasks::', "$name::$id", ACCESS_EDIT)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_publish: ' . _XTASKS_NOAUTH);
        return false;
    }
			
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    $xtaskstable = $pntable['xtasks'];

	$private = $task['private'];

    $sql = "UPDATE $xtaskstable
              SET xar_private = " . ($task['private'] ? "0" : "1") . ",
			  		xar_date_changed = '" . time() . "'
			WHERE xar_id = $id";

    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_publish: ' . _XTASKS_PUBLISHFAILED . '<br>' . $sql);
        return false;
    }

    $returnid = (pnModGetVar('xtasks','returnfrommigrate') ? $id : $task['parentid']);
	return $returnid;
}

function xtasks_adminapi_accept($args)
{
    extract($args);

    if (!isset($id)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_accept: ' . _MODARGSERROR);
        return false;
    }

    if (!pnModAPILoad('xtasks', 'user')) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_accept: ' . _LOADFAILED);
        return $output->GetOutput();
    }

    $task = pnModAPIFunc('xtasks',
						'user',
						'get',
						array('id' => $id));
			
	if ($task == false) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_accept: ' . _XTASKS_NOSUCHITEM);
        return $output->GetOutput();
    }

    if (!pnSecAuthAction(0, 'xTasks::task', '::$task[basetaskid]', ACCESS_EDIT)
			|| !pnSecAuthAction(0, 'xtasks::', "$name::$id", ACCESS_EDIT)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_accept: ' . _XTASKS_NOAUTH);
        return false;
    }
			
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    $xtaskstable = $pntable['xtasks'];

	// NEED CHECK TO ENSURE LOGGED IN
    $sql = "UPDATE $xtaskstable
              SET xar_owner = " . pnSessionGetVar('uid') . ",
			  		xar_date_changed = '" . time() . "'
			WHERE xar_id = $id";

    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_accept: ' . _XTASKS_ACCEPTFAILED . '<br>' . $sql);
        return false;
    }

    $returnid = (pnModGetVar('xtasks','returnfrommigrate') ? $id : $task['parentid']);
	return $returnid;
}

// DEPRECATED
/*
function xtasks_adminapi_delete($args)
{
    extract($args);

    if (!isset($id) || !is_numeric($id)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_delete: ' . _XTASKS_MODARGSERROR);
        return false;
    }

    if (!pnModAPILoad('xtasks', 'user')) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_delete: ' . _XTASKS_LOADFAILED);
        return $output->GetOutput();
    }

    // does it exist ?
    $task = pnModAPIFunc('xtasks',
							'user',
							'get',
							array('id' => $id));

    if ($task == false) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_delete: ' . _XTASKS_NOSUCHITEM);
        return $output->GetOutput();
    }

    if (!pnSecAuthAction(0, 'xTasks::task', '::$task[basetaskid]', ACCESS_DELETE)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_delete: ' . _XTASKS_NOAUTH);
        return false;
    }

    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    $xtaskstable = $pntable['xtasks'];

    $sql = "DELETE FROM $xtaskstable
            WHERE xar_id = " . pnVarPrepForStore($id);
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_delete: ' . _XTASKS_DELETEFAILED . '<br>' . $sql);
        return false;
    }

    $switchboardtable = $pntable['xtasks_switchboard'];

    $sql = "DELETE FROM $switchboardtable
            WHERE xar_id = " . pnVarPrepForStore($id);
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_delete: ' . _XTASKS_SWITCHBOARDDELETEFAILED . '<br>' . $sql);
        return false;
    }

    return true;
}
*/
function xtasks_adminapi_migrate($args)
{
    extract($args);

	if (!isset($targetfocus)) $targetfocus = 0;

	if (!pnModAPILoad('xtasks', 'user')) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_migrate: ' . _XTASKS_LOADFAILED);
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
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_migrate: ' . _XTASKS_NOAFFECTEDTASKS);
        return false;
	}
	
// NEED TO ADAPT TO ROOT TASK PERMISSIONS USING GETROOT AND GET
	if(empty($parentid)) {
		$id = ($targetfocus > 0 ? $targetfocus : $affectedid);
	} else $id = $parentid;
// echo "id: $id, parentid: $parentid, targetfocus: $targetfocus, affectedid: $affectedid<br>";
    $parenttask = pnModAPIFunc('xtasks',
							'user',
							'get',
							array('id' => $id));
			
	if ($parenttask == false) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _XTASKS_NOSUCHITEM);
        return false;
    }

    if (!pnSecAuthAction(0, 'xTasks::task', '::$parenttask[basetaskid]', ACCESS_MODERATE)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_migrate: ' . $parenttask['basetaskid'] .  _XTASKS_NOAUTH);
        return false;
    }

    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    $taskstable = $pntable['xtasks'];
	
	if($targetfocus > 0) {
		// - 1 => Migrate selected tasks under taskfocus (taskfocus[any] = 1)
		$sql = "UPDATE $taskstable SET xar_parentid = " . ($targetfocus ? $targetfocus : "0") . " WHERE xar_id IN (" . implode(",",$affectedtasks) . ")";

		$dbconn->Execute($sql);
	
		if ($dbconn->ErrorNo() != 0) {
			pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_migrate: ' . _XTASKS_REFOCUSFAILED . '<br>' . $sql);
			return false;
		}
		$returnid = (pnModGetVar('xtasks','returnfrommigrate') ? $targetfocus : $parentid);
		return $returnid;

	} elseif($taskoption == 1) {
		// - 2 => Surface selected tasks to current task's parentid (taskoption = 1)
		// UH, THERE IS NO PARENTID PASSED
		$sql = "UPDATE $taskstable SET xar_parentid = " . ($parenttask['parentid'] ? $parenttask['parentid'] : "0") . " WHERE xar_id IN (" . implode(",",$affectedtasks) . ")";

		$dbconn->Execute($sql);
	
		if ($dbconn->ErrorNo() != 0) {
			pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_migrate: ' . _XTASKS_SURFACEFAILED . '<br>' . $sql);
			return false;
		}
		$returnid = (pnModGetVar('xtasks','returnfromsurface') ? $parentid : $parenttask['parentid']);
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
			
			if ($dbconn->ErrorNo() != 0) {
				pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_migrate: ' . _XTASKS_DELETEFAILED . '<br>' . $sql);
				return false;
			}
			
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
	
				$dbconn->Execute($sql);
			
				if ($dbconn->ErrorNo() != 0) {
					pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_migrate: ' . _XTASKS_DELETEFAILED . '<br>' . $sql);
					return false;
				}
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

		$dbconn->Execute($sql);
	
		if ($dbconn->ErrorNo() != 0) {
			pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_migrate: ' . _XTASKS_SURFACEFAILED . '<br>' . $sql);
			return false;
		}

		$sql = "DELETE FROM $taskstable WHERE xar_id IN (" . implode(",",$affectedtasks) . ")";

		$dbconn->Execute($sql);
	
		if ($dbconn->ErrorNo() != 0) {
			pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>xtasks_adminapi_migrate: ' . _XTASKS_DELETEFAILED . '<br>' . $sql);
			return false;
		}
		
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
?>
<?php
// ONLY ODD FUNCTION IS GETTASKMEMBERS

function tasks_userapi_getall($args)
{
    extract($args);
	
    if ($modname = "tasks") {
        $modname = "";
    }
	
	if(empty($parentid) || !is_numeric($parentid)) $parentid = "0";
	
    $tasks = array();
	$maxlevel = pnSessionGetVar('maxlevel');
	if($displaydepth > $maxlevel) {
		return tasks;
	}
	
    if (!pnSecAuthAction(0, 'tasks::', '::', ACCESS_OVERVIEW)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_userapi_getall: ' . _TASKS_NOAUTH);
        return $tasks;
    }

    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    $taskstable = $pntable['tasks'];

    $sql = "SELECT xar_id,
                   xar_parentid,
                   xar_modname,
                   xar_objectid,
                   xar_name,
                   xar_description,
                   xar_status,
                   xar_priority,
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
                   xar_hours_remaining
            FROM $taskstable
			WHERE xar_modname = '" . pnVarPrepForStore($modname) . "'
			" . ($objectid ? " AND xar_objectid = " . $objectid : "");

	// IMPLEMENT FILTER CODE FOR WHERE CLAUSE
	// FORCING PARENT ID CHECK FOR USE IN DRILLDOWNS
	// ENABLEING GLOBAL TASK STACK SEARCH BASED ON MODNAME/OBJECTID 
	$userId = pnSessionGetVar('uid');
	$filter = pnSessionGetVar('filter');
	switch($filter) {
		case 1: // My Tasks
			$sql .= ($parentid ? " AND xar_parentid = " . $parentid : "") . "
				 	AND (xar_creator = " . ($userId ? $userId : "0") . "
						OR xar_owner = " . ($userId ? $userId : "0") . "
						OR xar_assigner = " . ($userId ? $userId : "0") . ")
					AND xar_status = 0
					ORDER BY xar_priority DESC, xar_name";
			break;
		case 2: // Available Tasks
			$sql .= ($parentid ? " AND xar_parentid = " . $parentid : "") . "
				 	AND xar_owner = 0
					AND xar_status = 0
					ORDER BY xar_priority DESC, xar_name";
			break;
		case 3: // Priority List
			$sql .= ($parentid ? " AND xar_parentid = " . $parentid : "") . "
				 	AND xar_status = 0
					ORDER BY xar_priority DESC, xar_name";
			break;
		case 4: // Recent Activity - NEED DATE RANGE CRAP FROM CONFIG
			$sql .= ($parentid ? " AND xar_parentid = " . $parentid : "") . "
				 	ORDER BY xar_status, xar_priority DESC, xar_name";
			break;
		case 5:
			$sql .= " AND xar_parentid = " . ($parentid ? $parentid : "0") . "
					 ORDER BY xar_status, xar_priority DESC, xar_name";
			break;
		case 0:
		default:
			$sql .= " AND xar_parentid = " . ($parentid ? $parentid : "0") . "
					 ORDER BY xar_status, xar_priority DESC, xar_name";
	
	}

    $result = $dbconn->SelectLimit($sql, -1, 0);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_userapi_getall: ' . _TASKS_GETFAILED . '<br>' . $sql);
        return false;
    }

    for (; !$result->EOF; $result->MoveNext()) {
        list($id,
			   $parentid,
			   $modname,
			   $objectid,
			   $name,
			   $description,
			   $status,
			   $priority,
			   $private,
			   $creator,
			   $owner,
			   $assigner,
			   $date_created,
			   $date_approved,
			   $date_changed,
			   $date_start_planned,
			   $date_start_actual,
			   $date_end_planned,
			   $date_end_actual,
			   $hours_planned,
			   $hours_spent,
			   $hours_remaining) = $result->fields;
     $basetaskid = pnModAPIFunc('tasks', 'user', 'getroot', array('id' => $id));
        if (pnSecAuthAction(0, 'tasks::task', '$modname:$objectid:$basetaskid', ACCESS_READ)) {
			$ttlsubtasks = pnModAPIFunc('tasks', 'user', 'countitems', array('parentid' => $id));
			$closedsubtasks = pnModAPIFunc('tasks', 'user', 'countitems', array('parentid' => $id, 'statustype' => 'closed'));
			$opensubtasks = pnModAPIFunc('tasks', 'user', 'countitems', array('parentid' => $id, 'statustype' => 'open'));
            $subtasks = pnModAPIFunc('tasks',
									'user', 
									'getall', 
									array('parentid' => $id, 
										'modname' => $modname,
										'objectid' => $objectid,
										'displaydepth' => $displaydepth + 1));
			$tasks[] = array('id' => $id,
							'depth' => $displaydepth,
                             'parentid' => $parentid,
                             'basetaskid' => $basetaskid,
                             'modname' => $modname,
							 'objectid' => $objectid,
                             'name' => $name,
							 'description' => $description,
							 'status' => $status,
							 'priority' => $priority,
							 'private' => $private,
							 'creator' => $creator,
							 'owner' => $owner,
							 'assigner' => $assigner,
							 'date_created' => $date_created,
							 'date_approved' => $date_approved,
							 'date_changed' => $date_changed,
							 'date_start_planned' => $date_start_planned,
							 'date_start_actual' => $date_start_actual,
							 'date_end_planned' => $date_end_planned,
							 'date_end_actual' => $date_end_actual,
							 'hours_planned' => $hours_planned,
							 'hours_spent' => $hours_spent,
							 'hours_remaining' => $hours_remaining,
							 'ttlsubtasks' => $ttlsubtasks,
							 'closedsubtasks' => $closedsubtasks,
							 'opensubtasks' => $opensubtasks);
	// NEED TO PREVENT THIS WHEN IT IS NOT NEEDED
	// UNLESS USED TO POPULATE AN INITIAL TEMPORARY WORKING TABLE
			if(is_array($subtasks) && count($subtasks) > 0) {
				foreach($subtasks as $appendedtask) {
					$tasks[] = $appendedtask;
				}
			}
        }
    }

    $result->Close();

    return $tasks;
}

function tasks_userapi_get($args)
{
    extract($args);

    if (!isset($id) || !is_numeric($id)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_userapi_get: ' . _TASKS_MODARGSERROR);
        return false;
    }

    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    $taskstable = $pntable['tasks'];

    $sql = "SELECT xar_id,
					xar_parentid,
                   xar_modname,
                   xar_objectid,
                   xar_name,
                   xar_description,
                   xar_status,
                   xar_priority,
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
                   xar_hours_remaining
			FROM $taskstable
            WHERE xar_id = " . $id;
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_userapi_get: ' . _TASKS_GETFAILED . '<br>' . $sql);
        return false;
    }

    if ($result->EOF) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_userapi_get: ' . _TASKS_NOSUCHITEM . ': ' . $id);
        return false;
    }

	list($id,
		   $parentid,
		   $modname,
		   $objectid,
		   $name,
		   $description,
		   $status,
		   $priority,
		   $private,
		   $creator,
		   $owner,
		   $assigner,
		   $date_created,
		   $date_approved,
		   $date_changed,
		   $date_start_planned,
		   $date_start_actual,
		   $date_end_planned,
		   $date_end_actual,
		   $hours_planned,
		   $hours_spent,
		   $hours_remaining) = $result->fields;
		
	$basetaskid = pnModAPIFunc('tasks', 'user', 'getroot', array('id' => $id));
    if (!pnSecAuthAction(0, 'tasks::task', '$modname:$objectid:$basetaskid', ACCESS_READ)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_userapi_get: ' . _TASKS_NOAUTH);
        return;
    }

	$result->Close();
	
	$ttlsubtasks = pnModAPIFunc('tasks', 'user', 'countitems', array('parentid' => $id));
	$closedsubtasks = pnModAPIFunc('tasks', 'user', 'countitems', array('parentid' => $id, 'statustype' => 'closed'));
	$opensubtasks = pnModAPIFunc('tasks', 'user', 'countitems', array('parentid' => $id, 'statustype' => 'open'));

    $task = array('id' => $id,
					 'parentid' => $parentid,
					 'modname' => $modname,
					 'objectid' => $objectid,
					 'name' => $name,
					 'description' => $description,
					 'status' => $status,
					 'priority' => $priority,
					 'private' => $private,
					 'creator' => $creator,
					 'owner' => $owner,
					 'assigner' => $assigner,
					 'date_created' => $date_created,
					 'date_approved' => $date_approved,
					 'date_changed' => $date_changed,
					 'date_start_planned' => $date_start_planned,
					 'date_start_actual' => $date_start_actual,
					 'date_end_planned' => $date_end_planned,
					 'date_end_actual' => $date_end_actual,
					 'hours_planned' => $hours_planned,
					 'hours_spent' => $hours_spent,
					 'hours_remaining' => $hours_remaining,
					 'ttlsubtasks' => $ttlsubtasks,
					 'closedsubtasks' => $closedsubtasks,
					 'opensubtasks' => $opensubtasks,
					 'basetaskid'	=> $basetaskid);

    return $task;
}

function tasks_userapi_countitems($args)
{
	extract($args);
	
	if(empty($parentid)) $parentid = "0";
	
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    $taskstable = $pntable['tasks'];
    $taskscolumn = &$pntable['tasks_column'];

    $sql = "SELECT COUNT(1)
            FROM $taskstable
			WHERE xar_parentid = $parentid";

	switch($statustype) {
		case "open":
			$sql .= " AND xar_status = 0";
			break;
		case "closed":
			$sql .= " AND xar_status = 1";
			break;
	} // ELSE GET ALL

    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        return false;
    }

    list($numtasks) = $result->fields;

    $result->Close();

    return $numtasks;
}

function tasks_userapi_getroot($args)
{
	extract($args);
	
	if (!isset($id) || !is_numeric($id)) {
        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>tasks_userapi_getroot: ' . _TASKS_MODARGSERROR);
        return false;
    }
	
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    $taskstable = $pntable['tasks'];
    $taskscolumn = &$pntable['tasks_columns'];
	$rootid = $id;
	while($rootid != 0) {
		$sql = "SELECT $taskscolumn[id],
					$taskscolumn[parentid]
				FROM $taskstable
				WHERE xar_id = $rootid";
		$result = $dbconn->Execute($sql);
	
		if ($dbconn->ErrorNo() != 0) {
	        pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_ROOTFAILED . '<br>' . $sql);
			return $id;
		}
	
		list($parentid, $rootid) = $result->fields;
	}
	
    $result->Close();

    return $parentid;
}
?>
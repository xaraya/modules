<?php

function xproject_tasksapi_get($args)
{
    extract($args);

    if (!isset($taskid) || !is_numeric($taskid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item ID', 'tasks', 'get', 'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $taskstable = $xartable['xproject_tasks'];

    $sql = "SELECT xar_taskid,
                   xar_parentid,
                   xar_projectid,
                   xar_name,
				   xar_status,
				   xar_priority,
				   xar_description,
				   xar_private,
				   xar_creator,
				   xar_owner,
				   xar_assigner,
				   xar_groupid,
				   xar_date_created,
				   xar_date_approved,
				   xar_date_changed,
				   xar_date_start_planned,
				   xar_date_start_actual,
				   xar_date_end_planned,
				   xar_date_end_actual,
				   xar_hours_planned,
				   xar_hours_spent,
				   xar_hours_remaining,
				   xar_cost,
				   xar_recurring,
				   xar_periodicity,
				   xar_reminder
			FROM $taskstable
            WHERE xar_taskid = " . $taskid;
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

	list($taskid,
		   $parentid,
		   $projectid,
		   $name,
		   $status,
		   $priority,
		   $description,
		   $private,
		   $creator,
		   $owner,
		   $assigner,
		   $groupid,
		   $date_created,
		   $date_approved,
		   $date_changed,
		   $date_start_planned,
		   $date_start_actual,
		   $date_end_planned,
		   $date_end_actual,
		   $hours_planned,
		   $hours_spent,
		   $hours_remaining,
		   $cost,
		   $recurring,
		   $periodicity,
		   $reminder) = $result->fields;
		
    $result->Close();

    if (!xarSecAuthAction(0, 'xproject::Tasks', "$name::$taskid", ACCESS_READ)) {
        $msg = xarML('Not authorized to access #(1) item #(2)',
                    'xproject', xarVarPrepForStore($projectid));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
	
	$numsubtasks = xarModAPIFunc('xproject', 'tasks', 'countitems', array('parentid' => $taskid));
	
    $task = array('taskid' => $taskid,
				 'parentid' => $parentid,
				 'projectid' => $projectid,
				 'name' => $name,
				 'status' => $status,
				 'priority' => $priority,
				 'description' => $description,
				 'private' => $private,
				 'creator' => $creator,
				 'owner' => $owner,
				 'assigner' => $assigner,
				 'groupid' => $groupid,
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
				 'cost' => $cost,
				 'recurring' => $recurring,
				 'periodicity' => $periodicity,
				 'reminder' => $reminder,
				 'numsubtasks' => $numsubtasks);

    return $task;
}

?>

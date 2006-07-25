<?php

function xproject_tasksapi_update($args)
{
    extract($args);

    $invalid = array();
    if (!isset($projectid) || !is_numeric($projectid)) {
        $invalid[] = 'item ID';
    }
    if (!isset($name) || !is_string($name)) {
        $invalid[] = 'name';
    }
    if (!isset($taskid) || $taskid == 0) {
        $invalid[] = 'taskid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'tasks', 'update', 'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $task = xarModAPIFunc('xproject',
						'tasks',
						'get',
						array('taskid' => $taskid));
			
	if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecAuthAction(0, 'xproject::Project', "$task[name]::$taskid", ACCESS_EDIT)) {
        $msg = xarML('Not authorized to edit #(1) item #(2)',
                    'xproject', xarVarPrepForStore($projectid));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    if (!xarSecAuthAction(0, 'xproject::Project', "$name::$taskid", ACCESS_EDIT)) {
        $msg = xarML('Not authorized to edit #(1) item #(2)',
                    'xproject', xarVarPrepForStore($projectid));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
		
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $taskstable = $xartable['xproject_tasks'];

    $sql = "UPDATE $taskstable
              SET xar_parentid = " . ($parentid ? $parentid : "0") . ",
			   xar_projectid = " . $projectid . ",
			   xar_name = '" . xarVarPrepForStore($name) . "',
			   xar_status = " . $status . ",
			   xar_priority = " . $priority . ",
			   xar_description = '" . xarVarPrepForStore($description) . "',
			   xar_date_changed = " . time() . "
			WHERE xar_taskid = $taskid";
/*
			   xar_date_start_planned = " . ($date_start_planned ? $date_start_planned : "NULL") . ",
			   xar_date_start_actual = " . ($date_start_actual ? $date_start_actual : "NULL") . ",
			   xar_date_end_planned = " . ($date_end_planned ? $date_end_planned : "NULL") . ",
			   xar_date_end_actual = " . ($date_end_actual ? $date_end_actual : "NULL") . ",
			   xar_hours_planned = " . ($hours_planned ? $hours_planned : "NULL") . ",
			   xar_hours_spent = " . ($hours_spent ? $hours_spent : "NULL") . ",
			   xar_hours_remaining = " . ($hours_remaining ? $hours_remaining : "NULL") . ",
			   xar_cost = " . ($cost ? $cost : "NULL") . ",
			   xar_recurring = " . ($recurring ? $recurring : "NULL") . ",
			   xar_periodicity = " . ($periodicity ? $periodicity : "NULL") . ",
			   xar_reminder = " . ($reminder ? $reminder : "NULL") . "
			WHERE xar_taskid = $taskid";
*/
    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $item['module'] = 'xproject';
    $item['itemid'] = $projectid;
    $item['name'] = $name;
    $item['description'] = $description;
    xarModCallHooks('item', 'update', $projectid, $item);

    return true;
}

?>

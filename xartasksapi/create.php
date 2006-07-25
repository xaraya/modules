<?php

function xproject_tasksapi_create($args)
{
    extract($args);

    $invalid = array();
    if (!isset($name) || !is_string($name)) {
        $invalid[] = 'name';
    }
    if (!isset($projectid) || $projectid == 0) {
        $invalid[] = 'projectid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'tasks', 'create', 'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
	
	// TODO: NEED TO WORK IN PROJECT NAME, CURRENTLY USING TASK NAME
    if (!xarSecAuthAction(0, 'xproject::Project', "$name::$projectid", ACCESS_ADD)) {
        $msg = xarML('Not authorized to add #(1) items',
                    'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
		
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $taskstable = $xartable['xproject_tasks'];
	$xprojecttable = $xartable['xproject'];
    $nextId = $dbconn->GenId($xprojecttable);

    $sql = "INSERT INTO $taskstable (
               xar_taskid,
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
			   xar_reminder)
            VALUES (
              $nextId,
			  " . ($parentid ? $parentid : 0) . ",
			  $projectid,
              '" . xarVarPrepForStore($name) . "',
              " . xarVarPrepForStore($status) . ",
              " . xarVarPrepForStore($priority) . ",
              '" . xarVarPrepForStore($description) . "',
              " . ($private ? $private : "NULL") . ",
              " . xarSessionGetVar('uid') . ",
              " . xarSessionGetVar('uid') . ",
              " . xarSessionGetVar('uid') . ",
              NULL,
              " . time() . ",
			  NULL,
			  " . time() . ",
			  NULL,
			  NULL,
			  NULL,
			  NULL,
			  NULL,
			  NULL,
			  NULL,
			  0,
			  NULL,
			  NULL,
			  NULL)";			  

// PRIVATE INITIALLY SET BASED ON USER PREFERENCE

    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $taskid = $dbconn->PO_Insert_ID($xprojecttable, 'xar_projectid');

    $item = $args;
    $item['module'] = 'xproject';
    $item['itemid'] = $taskid;
    xarModCallHooks('item', 'create', $taskid, $item);

    return $taskid;
}

?>

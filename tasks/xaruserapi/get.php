<?php
/**
 * Get one task
 *
 */
function tasks_userapi_get($args)
{
    extract($args);

    if (!isset($id) || !is_numeric($id)) {
        xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>tasks_userapi_get: ' . xarML("Module argument error"));
        return false;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $taskstable = $xartable['tasks'];

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
    $result =& $dbconn->Execute($sql);
    if (!$result) return;

    if ($result->EOF) {
        xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>tasks_userapi_get: ' . xarML("No such item") . ': ' . $id);
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
		
	$basetaskid = xarModAPIFunc('tasks', 'user', 'getroot', array('id' => $id));
//     if (!xarSecAuthAction(0, 'tasks::task', '$modname:$objectid:$basetaskid', ACCESS_READ)) {
//         xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>tasks_userapi_get: ' . _TASKS_NOAUTH);
//         return;
//     }

	$result->Close();
	
	$ttlsubtasks = xarModAPIFunc('tasks', 'user', 'countitems', array('parentid' => $id));
	$closedsubtasks = xarModAPIFunc('tasks', 'user', 'countitems', array('parentid' => $id, 'statustype' => 'closed'));
	$opensubtasks = xarModAPIFunc('tasks', 'user', 'countitems', array('parentid' => $id, 'statustype' => 'open'));

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

?>
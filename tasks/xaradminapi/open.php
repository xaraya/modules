<?php
/**
 * Open a task
 *
 */
function tasks_adminapi_open($args)
{
    extract($args);

    if (!isset($id)) {
        xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>tasks_adminapi_open: ' . xarML("Module argument error"));
        return false;
    }

    $task = xarModAPIFunc('tasks',
						'user',
						'get',
						array('id' => $id));
			
	if ($task == false) {
        xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>tasks_adminapi_open: ' . xarML("No such item"));
        return $output->GetOutput();
    }

//     if (!xarSecAuthAction(0, 'tasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_MODERATE)) {
//         xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>tasks_adminapi_open: ' . _TASKS_NOAUTH);
//         return false;
//     }
			
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $taskstable = $xartable['tasks'];

    $sql = "UPDATE $taskstable
              SET xar_status = 0,
			  		xar_date_changed = '" . time() . "'
			WHERE xar_id = $id";

    $res =& $dbconn->Execute($sql);
    if (!$res) return;

    $returnid = (xarModGetVar('tasks','returnfrommigrate') ? $id : $task['parentid']);
	return $task['parentid'];
}

?>
<?php
/**
 * Approve a task
 *
 */
function tasks_adminapi_approve($args)
{
    extract($args);

    if (!isset($id)) {
        xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>tasks_adminapi_approve: ' . xarML("Module argument error"));
        return false;
    }

    $task = xarModAPIFunc('tasks',
						'user',
						'get',
						array('id' => $id));
			
	if ($task == false) {
        xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>tasks_adminapi_approve: ' . xarML("No such item"));
        return $output->GetOutput();
    }

//     if (!xarSecAuthAction(0, 'tasks::task', '::$task[basetaskid]', ACCESS_EDIT)
// 			|| !xarSecAuthAction(0, 'tasks::', "$name::$id", ACCESS_EDIT)) {
//         xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>tasks_adminapi_approve: ' . _TASKS_NOAUTH);
//         return false;
//     }
			
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $taskstable = $xartable['tasks'];

    $sql = "UPDATE $taskstable
              SET xar_date_approved = '" . time() . "'
			  		xar_date_changed = '" . time() . "'
			WHERE xar_id = $id";

    $res =& $dbconn->Execute($sql);
    if (!$res) return;

    $returnid = (xarModGetVar('tasks','returnfrommigrate') ? $id : $task['parentid']);
	return $returnid;
}

?>
<?php
/**
 * Accept a task
 *
 */
function tasks_adminapi_accept($args)
{
    extract($args);

    if (!isset($id)) {
        xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>tasks_adminapi_accept: ' . xarML("Module argument error"));
        return false;
    }

    $task = xarModAPIFunc('tasks',
						'user',
						'get',
						array('id' => $id));
			
	if ($task == false) {
        xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>tasks_adminapi_accept: ' . xarML("No such item"));
        return $output->GetOutput();
    }

 //    if (!xarSecAuthAction(0, 'tasks::task', '::$task[basetaskid]', ACCESS_EDIT)
// 			|| !xarSecAuthAction(0, 'tasks::', "$name::$id", ACCESS_EDIT)) {
//         xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>tasks_adminapi_accept: ' . _TASKS_NOAUTH);
//         return false;
//     }
			
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $taskstable = $xartable['tasks'];

	// NEED CHECK TO ENSURE LOGGED IN
    $sql = "UPDATE $taskstable
              SET xar_owner = " . xarSessionGetVar('uid') . ",
			  		xar_date_changed = '" . time() . "'
			WHERE xar_id = $id";

    $res =& $dbconn->Execute($sql);
    if (!$res) return;

    $returnid = (xarModGetVar('tasks','returnfrommigrate') ? $id : $task['parentid']);
	return $returnid;
}

?>
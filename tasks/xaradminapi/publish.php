<?php
/**
 * Publish a task
 *
 */
function tasks_adminapi_publish($args)
{
    extract($args);

    if (!isset($id)) {
        xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>tasks_adminapi_publish: ' . xarML("Module argument error"));
        return false;
    }

    $task = xarModAPIFunc('tasks',
						'user',
						'get',
						array('id' => $id));
			
	if ($task == false) {
        xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>tasks_adminapi_publish: ' . xarML("No such item"));
        return $output->GetOutput();
    }

//     if (!xarSecAuthAction(0, 'tasks::task', '::$task[basetaskid]', ACCESS_EDIT)
// 			|| !xarSecAuthAction(0, 'tasks::', "$name::$id", ACCESS_EDIT)) {
//         xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>tasks_adminapi_publish: ' . _TASKS_NOAUTH);
//         return false;
//     }
			
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $taskstable = $xartable['tasks'];

	$private = $task['private'];

    $sql = "UPDATE $taskstable
              SET xar_private = " . ($task['private'] ? "0" : "1") . ",
			  		xar_date_changed = '" . time() . "'
			WHERE xar_id = $id";

    $res = & $dbconn->Execute($sql);
    if (!$res) return;

    $returnid = (xarModGetVar('tasks','returnfrommigrate') ? $id : $task['parentid']);
	return $returnid;
}

?>
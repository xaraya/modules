<?php
/**
 * Update a task
 *
 */
function tasks_adminapi_update($args)
{
    extract($args);

    if (!isset($id)) {
        xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>tasks_adminapi_update: ' . xarML("Module argument error"));
        return false;
    }

    $task = xarModAPIFunc('tasks',
                        'user',
                        'get',
                        array('id' => $id));
            
    if ($task == false) {
        xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>tasks_adminapi_update: ' . xarML("No such item"));
        return $output->GetOutput();
    }

//     if (!xarSecAuthAction(0, 'tasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_COMMENT)) {
//         xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>tasks_adminapi_update: ' . _TASKS_NOAUTH);
//         return false;
//     }
            
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $taskstable = $xartable['tasks'];

    $sql = "UPDATE $taskstable
              SET xar_name = ?,
                  xar_status = ?,
                  xar_priority = ?,
                  xar_description = ?
            WHERE xar_id = $id";
    $bindvars = array($name, $status, $priority, $description);

    $res =& $dbconn->Execute($sql);
    if (!$res) return;

    $returnid = (xarModGetVar('tasks','returnfromedit') ? $id : $task['parentid']);
    return $returnid;
}

?>
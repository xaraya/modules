<?php

function xproject_tasksapi_delete($args)
{
    extract($args);

    if (!isset($taskid) || !is_numeric($taskid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item ID', 'tasks', 'delete', 'xproject');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    // does it exist ?
    $task = xarModAPIFunc('xproject',
							'tasks',
							'get',
							array('taskid' => $taskid));

    if (!isset($task) && xarExceptionMajor() != XAR_NO_EXCEPTION) return;

    if (!xarSecAuthAction(0, 'xproject::Tasks', "$task[name]::$taskid", ACCESS_DELETE)) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'xproject', xarVarPrepForStore($projectid));
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $taskstable = $xartable['xproject_tasks'];

    // does it have children ?
    $sql = "DELETE FROM $taskstable
            WHERE xar_taskid = " . xarVarPrepForStore($taskid);
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $item['module'] = 'xproject';
    $item['itemid'] = $taskid;
    xarModCallHooks('item', 'delete', $taskid, $item);

    // Let the calling process know that we have finished successfully
    return true;
}
?>
<?php

function xproject_tasksapi_delete($args)
{
    extract($args);

    if (!isset($taskid) || !is_numeric($taskid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item ID', 'tasks', 'delete', 'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    // does it exist ?
    $task = xarModAPIFunc('xproject',
							'tasks',
							'get',
							array('taskid' => $taskid));

    if (!isset($task) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecAuthAction(0, 'xproject::Tasks', "$task[name]::$taskid", ACCESS_DELETE)) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'xproject', xarVarPrepForStore($projectid));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $taskstable = $xartable['xproject_tasks'];

    // does it have children ?
    $sql = "DELETE FROM $taskstable
            WHERE xar_taskid = $taskid";
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
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

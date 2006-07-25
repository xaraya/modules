<?php

function xtasks_tasks_delete($args)
{
    list($taskid,
         $objectid,
         $confirm) = xarVarCleanFromInput('taskid',
										  'objectid',
										  'confirm');

    extract($args);

     if (!empty($objectid)) {
         $taskid = $objectid;
     }                     

    if (!xarModLoad('xtasks', 'user')) return;

    $task = xarModAPIFunc('xtasks',
                         'tasks',
                         'get',
                         array('taskid' => $taskid));

    if (!isset($task) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecAuthAction(0, 'xtasks::Tasks', "$task[name]::$taskid", ACCESS_DELETE)) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'xtasks', xarVarPrepForDisplay($tid));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    if (empty($confirm)) {
        $data = xarModAPIFunc('xtasks','user','menu');

        $data['projectid'] = $task['projectid'];
        $data['taskid'] = $task['taskid'];

        $data['taskname'] = xarVarPrepForDisplay($task['name']);
        $data['confirmbutton'] = xarML('Confirm');

        $data['authid'] = xarSecGenAuthKey();

        return $data;
	}
	if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for deleting #(1) item #(2)',
                    'xtasks', xarVarPrepForDisplay($projectid));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    if (!xarModAPIFunc('xtasks',
                     'tasks',
                     'delete',
                     array('taskid' => $taskid))) {
        return;
    }
    xarSessionSetVar('statusmsg', xarML('Task Deleted'));

    xarResponseRedirect(xarModURL('xtasks', 'user', 'display', array('projectid' => $task['projectid'], 'taskid' => $task['parentid'])));

    return true;
}
?>

<?php

function xproject_tasks_delete($args)
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

    if (!xarModLoad('xproject', 'user')) return;

    $task = xarModAPIFunc('xproject',
                         'tasks',
                         'get',
                         array('taskid' => $taskid));

    if (!isset($task) && xarExceptionMajor() != XAR_NO_EXCEPTION) return;

    if (!xarSecAuthAction(0, 'xproject::Tasks', "$task[name]::$taskid", ACCESS_DELETE)) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'xproject', xarVarPrepForDisplay($tid));
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    if (empty($confirm)) {
        $data = xarModAPIFunc('xproject','user','menu');

        $data['projectid'] = $task['projectid'];
        $data['taskid'] = $task['taskid'];

        $data['taskname'] = xarVarPrepForDisplay($task['name']);
        $data['confirmbutton'] = xarML('Confirm');

        $data['authid'] = xarSecGenAuthKey();

        return $data;
	}
	if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for deleting #(1) item #(2)',
                    'xproject', xarVarPrepForDisplay($projectid));
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    if (!xarModAPIFunc('xproject',
                     'tasks',
                     'delete',
                     array('taskid' => $taskid))) {
        return;
    }
    xarSessionSetVar('statusmsg', xarMLByKey('Task Deleted'));

    xarResponseRedirect(xarModURL('xproject', 'user', 'display', array('projectid' => $task['projectid'], 'taskid' => $task['parentid'])));

    return true;
}
?>

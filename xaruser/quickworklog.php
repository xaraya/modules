<?php

function xtasks_user_quickworklog($args)
{
    extract($args);
    
    if (!xarVarFetch('taskid',     'str::',     $taskid)) return;

    if (!xarModAPILoad('xtasks', 'user')) return;
    
    $data = xarModAPIFunc('xtasks','admin','menu');

    if (!xarSecurityCheck('RecordWorklog')) {
        return;
    }

    $taskinfo = xarModAPIFunc('xtasks',
                          'user',
                          'get',
                          array('taskid' => $taskid));

    $worklog = xarModAPIFunc('xtasks',
                          'worklog',
                          'getallfromtask',
                          array('taskid' => $taskid));

    $data['authid'] = xarSecGenAuthKey();
    $data['taskid'] = $taskid;
    $data['taskinfo'] = $taskinfo;
    $data['worklog'] = $worklog;
    $data['returnurl'] = xarModURL('xtasks', 'user', 'quick');
    $data['addbutton'] = xarVarPrepForDisplay(xarML('Submit Work'));

    return $data;
}

?>
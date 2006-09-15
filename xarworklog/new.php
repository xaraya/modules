<?php

function xtasks_worklog_new($args)
{
    extract($args);

    if (!xarVarFetch('taskid',     'id',     $taskid)) return;

    if (!xarModAPILoad('xtasks', 'user')) return;
    
    $data = xarModAPIFunc('xtasks','admin','menu');

    if (!xarSecurityCheck('RecordWorklog')) {
        return;
    }

    $taskinfo = xarModAPIFunc('xtasks',
                          'user',
                          'get',
                          array('taskid' => $taskid));

    $data['authid'] = xarSecGenAuthKey();
    $data['taskid'] = $taskid;
    $data['taskinfo'] = $taskinfo;

    $data['addbutton'] = xarVarPrepForDisplay(xarML('Submit Work'));

    return $data;
}

?>
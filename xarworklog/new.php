<?php

function xtasks_worklog_new($args)
{
    extract($args);

    if (!xarVarFetch('taskid',     'id',     $taskid)) return;
    if (!xarVarFetch('returnurl',     'str::',     $returnurl, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarModAPILoad('xtasks', 'user')) return;
    
    $data = xarModAPIFunc('xtasks','admin','menu');

    if (!xarSecurityCheck('RecordWorklog')) {
        return;
    }

    $taskinfo = xarModAPIFunc('xtasks',
                          'user',
                          'get',
                          array('taskid' => $taskid));
    
    $data['currentdate'] = date("Y-m-d H:i:s", xarMLS_userTime());

    $data['authid'] = xarSecGenAuthKey();
    $data['taskid'] = $taskid;
    $data['taskinfo'] = $taskinfo;
    $data['returnurl'] = $returnurl;

    $data['addbutton'] = xarVarPrepForDisplay(xarML('Submit Work'));
    
    $worklog = xarModAPIFunc('xtasks', 'worklog', 'getallfromtask', array('taskid' => $taskid));
    
    if($worklog === false) return;
    
    $data['worklog'] = $worklog;

    return $data;
}

?>
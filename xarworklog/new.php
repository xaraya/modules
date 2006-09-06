<?php

function xtasks_worklog_new($args)
{
    extract($args);

    if (!xarVarFetch('taskid',     'id',     $taskid)) return;

    if (!xarVarFetch('inline', 'int', $inline, 0, XARVAR_NOT_REQUIRED)) return;

    if (!xarModAPILoad('xtasks', 'user')) return;
    
    $data = xarModAPIFunc('xtasks','admin','menu');

    if (!xarSecurityCheck('AddXProject')) {
        return;
    }

    $taskinfo = xarModAPIFunc('xtasks',
                          'user',
                          'get',
                          array('taskid' => $taskid));

    $data['authid'] = xarSecGenAuthKey();
    $data['taskid'] = $taskid;
    $data['inline'] = $inline;
    $data['taskinfo'] = $taskinfo;

    $data['addbutton'] = xarVarPrepForDisplay(xarML('Submit Work'));

    return $data;
}

?>
<?php

function xtasks_reminders_new()
{
    if (!xarVarFetch('taskid',     'id',     $taskid)) return;
    if (!xarVarFetch('inline',     'bool',     $inline, 0)) return;
    if (!xarModAPILoad('xtasks', 'user')) return;
    
    $data = xarModAPIFunc('xtasks','admin','menu');

    if (!xarSecurityCheck('UseReminders')) {
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

    return $data;
}

?>

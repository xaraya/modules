<?php

function xtasks_reminders_new()
{
    if (!xarVarFetch('taskid',     'id',     $taskid)) return;

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
    $data['taskinfo'] = $taskinfo;

    $data['addbutton'] = xarVarPrepForDisplay(xarML('Add Reminder'));

    return $data;
}

?>
<?php

function xtasks_reminders_modify($args)
{
    extract($args);
    
    if (!xarVarFetch('reminderid',     'id',     $reminderid,     $reminderid,     XARVAR_NOT_REQUIRED)) return;

    if (!xarModAPILoad('xtasks', 'user')) return;
    
    $item = xarModAPIFunc('xtasks',
                         'reminders',
                         'get',
                         array('reminderid' => $reminderid));
    
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('UseReminders', 1, 'Item', "All:All:All")) {
        return;
    }

    $taskinfo = xarModAPIFunc('xtasks',
                          'user',
                          'get',
                          array('taskid' => $item['taskid']));
    
    $data = xarModAPIFunc('xtasks','admin','menu');
    
    $data['reminderid'] = $item['reminderid'];
    
    $data['authid'] = xarSecGenAuthKey();
    
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update'));

    $data['item'] = $item;
    
    $data['taskinfo'] = $taskinfo;
    
    return $data;
}

?>
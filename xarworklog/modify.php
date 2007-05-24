<?php

function xtasks_worklog_modify($args)
{
    extract($args);
    
    if (!xarVarFetch('worklogid',     'id',     $worklogid,     $worklogid,     XARVAR_NOT_REQUIRED)) return;

    if (!xarModAPILoad('xtasks', 'user')) return;
    
    $item = xarModAPIFunc('xtasks',
                         'worklog',
                         'get',
                         array('worklogid' => $worklogid));
    
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('AuditWorklog', 1, 'Item', "All:All:All")) {
        return;
    }

    $taskinfo = xarModAPIFunc('xtasks',
                          'user',
                          'get',
                          array('taskid' => $item['taskid']));
    
    $data = xarModAPIFunc('xtasks','admin','menu');
    
    $data['worklogid'] = $item['worklogid'];
    
    $data['authid'] = xarSecGenAuthKey();
    
    $data['item'] = $item;

    $data['taskinfo'] = $taskinfo;
    
    return $data;
}

?>

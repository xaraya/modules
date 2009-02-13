<?php

function xtasks_worklog_delete($args)
{
    extract($args);
    
    if (!xarVarFetch('worklogid', 'id', $worklogid)) return;
    if (!xarVarFetch('confirm', 'isset', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarModAPILoad('xtasks', 'user')) return;
    
    $item = xarModAPIFunc('xtasks',
                         'worklog',
                         'get',
                         array('worklogid' => $worklogid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;


    if (!xarSecurityCheck('AuditWorklog', 1, 'Item', "Al:All:Al")) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'xtasks', xarVarPrepForDisplay($worklogid));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    if (empty($confirm)) {

        $taskinfo = xarModAPIFunc('xtasks',
                              'user',
                              'get',
                              array('taskid' => $item['taskid']));
                              
        xarModLoad('xtasks','admin');
        $data = xarModAPIFunc('xtasks','admin','menu');

        $data['item'] = $item;
        $data['worklogid'] = $worklogid;
        $data['taskid'] = $item['taskid'];
        $data['taskinfo'] = $taskinfo;
        
        $data['confirmbutton'] = xarML('Confirm');

        $data['authid'] = xarSecGenAuthKey();

        return $data;
    }
    if (!xarSecConfirmAuthKey()) return;
    if (!xarModAPIFunc('xtasks',
                     'worklog',
                     'delete',
                     array('worklogid' => $worklogid))) {
        return;
    }
    xarSessionSetVar('statusmsg', xarML('Worklog Record Deleted'));

    xarResponseRedirect(xarModURL('xtasks', 'admin', 'display', array('taskid' => $item['taskid'], 'mode' => "worklog")));

    return true;
}

?>

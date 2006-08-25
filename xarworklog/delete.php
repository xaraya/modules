<?php

function xtasks_worklog_delete($args)
{
    extract($args);
    
    if (!xarVarFetch('reminderid', 'id', $reminderid)) return;
    if (!xarVarFetch('objectid', 'isset', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'isset', $confirm, '', XARVAR_NOT_REQUIRED)) return;


    if (!empty($objectid)) {
        $reminderid = $objectid;
    }

    if (!xarModAPILoad('xtasks', 'user')) return;
    
    $item = xarModAPIFunc('xtasks',
                         'reminders',
                         'get',
                         array('reminderid' => $reminderid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;


    if (!xarSecurityCheck('DeleteXProject', 1, 'Item', "$item[project_name]:All:$item[projectid]")) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'xproject', xarVarPrepForDisplay($projectid));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    if (empty($confirm)) {

        $projectinfo = xarModAPIFunc('xtasks',
                              'user',
                              'get',
                              array('projectid' => $item['projectid']));
                              
        xarModLoad('xtasks','admin');
        $data = xarModAPIFunc('xtasks','admin','menu');

        $data['reminderid'] = $reminderid;
        $data['projectinfo'] = $projectinfo;

        $data['reminder_name'] = xarVarPrepForDisplay($item['reminder_name']);
        $data['confirmbutton'] = xarML('Confirm');

        $data['authid'] = xarSecGenAuthKey();

        return $data;
    }
    if (!xarSecConfirmAuthKey()) return;
    if (!xarModAPIFunc('xtasks',
                     'reminders',
                     'delete',
                     array('reminderid' => $reminderid))) {
        return;
    }
    xarSessionSetVar('statusmsg', xarML('Page Deleted'));

    xarResponseRedirect(xarModURL('xtasks', 'admin', 'display', array('projectid' => $item['projectid'], 'mode' => "reminders")));

    return true;
}

?>

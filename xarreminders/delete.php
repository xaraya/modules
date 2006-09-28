<?php

function xtasks_reminders_delete($args)
{
    extract($args);
    
    if (!xarVarFetch('reminderid', 'id', $reminderid)) return;
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


    if (!xarSecurityCheck('UseReminders', 1, 'Item', "All:All:All")) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'xtasks', xarVarPrepForDisplay($projectid));
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

        $data['reminderid'] = $reminderid;
        $data['taskinfo'] = $taskinfo;

        $data['item'] = $item;
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

    xarResponseRedirect(xarModURL('xtasks', 'admin', 'display', array('taskid' => $item['taskid'], 'mode' => "reminders")));

    return true;
}

?>

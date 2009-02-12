<?php

function dossier_reminders_delete($args)
{
    extract($args);
    
    if (!xarVarFetch('reminderid', 'id', $reminderid)) return;
    if (!xarVarFetch('confirm', 'isset', $confirm, '', XARVAR_NOT_REQUIRED)) return;


    if (!empty($objectid)) {
        $reminderid = $objectid;
    }
    
    $item = xarModAPIFunc('dossier',
                         'reminders',
                         'get',
                         array('reminderid' => $reminderid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;


    if (!xarSecurityCheck('UseDossierReminders', 1, 'Reminders', "All:All:All:All")) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'xtasks', xarVarPrepForDisplay($projectid));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    if (empty($confirm)) {

        $contactinfo = xarModAPIFunc('dossier',
                              'user',
                              'get',
                              array('contactid' => $item['contactid']));
                              
        xarModLoad('dossier','admin');
        $data = xarModAPIFunc('dossier','admin','menu');

        $data['reminderid'] = $reminderid;
        $data['contactinfo'] = $contactinfo;

        $data['item'] = $item;
        $data['confirmbutton'] = xarML('Confirm');

        $data['authid'] = xarSecGenAuthKey();

        return $data;
    }
    if (!xarSecConfirmAuthKey()) return;
    if (!xarModAPIFunc('dossier',
                     'reminders',
                     'delete',
                     array('reminderid' => $reminderid))) {
        return;
    }
    xarSessionSetVar('statusmsg', xarML('Reminder Deleted'));

    xarResponseRedirect(xarModURL('dossier', 'admin', 'display', array('contactid' => $item['contactid'], 'mode' => "reminders")));

    return true;
}

?>

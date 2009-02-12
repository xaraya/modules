<?php

function dossier_logs_delete($args)
{
    extract($args);
    
    if (!xarVarFetch('logid', 'id', $logid)) return;
    if (!xarVarFetch('confirm', 'isset', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarModAPILoad('dossier', 'user')) return;
    
    $item = xarModAPIFunc('dossier',
                         'logs',
                         'get',
                         array('logid' => $logid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;


    if (!xarSecurityCheck('MyDossierLog', 1, 'Log', "All:All:All:All")) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'xtasks', xarVarPrepForDisplay($worklogid));
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

        $data['item'] = $item;
        $data['logid'] = $logid;
        $data['contactid'] = $item['contactid'];
        $data['contactinfo'] = $contactinfo;
        
        $data['confirmbutton'] = xarML('Confirm');

        $data['authid'] = xarSecGenAuthKey();

        return $data;
    }
    if (!xarSecConfirmAuthKey()) return;
    if (!xarModAPIFunc('dossier',
                     'logs',
                     'delete',
                     array('logid' => $logid))) {
        return;
    }
    xarSessionSetVar('statusmsg', xarML('Worklog Record Deleted'));

    xarResponseRedirect(xarModURL('dossier', 'admin', 'display', array('contactid' => $item['contactid'], 'mode' => "contactlog")));

    return true;
}

?>

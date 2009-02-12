<?php

function dossier_logs_update($args)
{
    extract($args);
    
    if (!xarVarFetch('logid', 'id', $logid)) return;
    if (!xarVarFetch('logdate', 'str:1:', $logdate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('logtype', 'str::', $logtype, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('notes', 'str::', $notes, '', XARVAR_NOT_REQUIRED)) return;
                                        
    if (!xarSecConfirmAuthKey()) return;
    
    $worklog = xarModAPIFunc('dossier', 'logs', 'get', array('logid' => $logid));
    
    if(!$worklog) return;
    
    if(!xarModAPIFunc('dossier',
                    'logs',
                    'update',
                    array('logid'       => $logid,
                        'logdate'       => $logdate,
                        'logtype'       => $logtype,
                        'notes'         => $notes))) {
        return;
    }


    xarSessionSetVar('statusmsg', xarML('Contact Record Updated'));

    xarResponseRedirect(xarModURL('dossier', 'admin', 'display', array('contactid' => $worklog['contactid'], 'mode' => "contactlog")));

    return true;
}

?>

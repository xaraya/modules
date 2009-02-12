<?php

function dossier_logs_create($args)
{
    extract($args);
    
    if (!xarVarFetch('contactid', 'id', $contactid, $contactid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ownerid', 'id', $ownerid, $ownerid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('logdate', 'str::', $logdate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('logtype', 'str::', $logtype, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('notes', 'str::', $notes, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str::', $returnurl, $returnurl, XARVAR_NOT_REQUIRED)) return;
    
    if(empty($returnurl)) $returnurl = xarModURL('dossier', 'admin', 'display', array('contactid' => $contactid, 'mode' => "contactlog"));
                                            
    if (!xarSecConfirmAuthKey()) return;
    
    if(!empty($logdate)) {
        if (!preg_match('/[a-zA-Z]+/',$logdate)) {
            $logdate .= ' GMT';
        }
        $logdate = strtotime($logdate);
        if ($logdate === false) $logdate = -1;
        if ($logdate >= 0) {
            // adjust for the user's timezone offset
            $logdate -= xarMLS_userOffset($logdate) * 3600;
        }
        $logdate = date("Y-m-d H:i:s", $logdate);
    }

    $worklogid = xarModAPIFunc('dossier',
                        'logs',
                        'create',
                        array('contactid'    => $contactid,
                            'ownerid'        => $ownerid,
                            'logdate'        => $logdate,
                            'logtype'        => $logtype,
                            'notes'          => $notes));


    if (!isset($worklogid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    xarSessionSetVar('statusmsg', xarMLByKey('CONTACTLOGCREATED'));

    xarResponseRedirect($returnurl);

    return true;
}

?>

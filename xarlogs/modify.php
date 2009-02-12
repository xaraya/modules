<?php

function dossier_logs_modify($args)
{
    extract($args);
    
    if (!xarVarFetch('logid',     'id',     $logid)) return;

    if (!xarModAPILoad('dossier', 'user')) return;
    
    $item = xarModAPIFunc('dossier',
                         'logs',
                         'get',
                         array('logid' => $logid));
    
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('MyDossierLog', 1, 'Log', "All:All:All:All")) {
        return;
    }

    $contactinfo = xarModAPIFunc('dossier',
                          'user',
                          'get',
                          array('contactid' => $item['contactid']));
    
    $data = xarModAPIFunc('dossier','admin','menu');
    
    $data['logid'] = $item['logid'];
    
    $data['authid'] = xarSecGenAuthKey();
    
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update'));

    $data['item'] = $item;

    $data['contactinfo'] = $contactinfo;
    
    return $data;
}

?>

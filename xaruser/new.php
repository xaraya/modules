<?php

function dossier_user_new()
{    
    if (!xarVarFetch('returnurl', 'str::', $returnurl, NULL, XARVAR_NOT_REQUIRED)) return;
    
    $data = xarModAPIFunc('dossier','admin','menu');

    $ownerid = xarUserGetVar('uid');
    if (!xarSecurityCheck('TeamDossierAccess', 1, 'Contact', "All:All:All:".$ownerid)) {//TODO: security
//    if (!xarSecurityCheck('TeamDossierAccess')) {
        return;
    }

    $data['authid'] = xarSecGenAuthKey();
    $data['webmasterid'] = xarSessionGetVar('uid');
    $data['returnurl'] = $returnurl;

    $data['addbutton'] = xarVarPrepForDisplay(xarML('Add Contact'));

    $item = array();
    $item['module'] = 'dossier';
    $hooks = xarModCallHooks('item','new','',$item);
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }

    return $data;
}

?>

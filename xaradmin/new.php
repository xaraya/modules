<?php

function dossier_admin_new()
{
    $data = xarModAPIFunc('dossier','admin','menu');

    $agentuid = xarUserGetVar('uid');
    if (!xarSecurityCheck('TeamDossierAccess', 1, 'Contact', "All:All:All:".$agentuid)) {//TODO: security
//    if (!xarSecurityCheck('TeamDossierAccess')) {
        return;
    }

    $data['authid'] = xarSecGenAuthKey();
    $data['webmasterid'] = xarSessionGetVar('uid');

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

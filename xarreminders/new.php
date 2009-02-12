<?php

function dossier_reminders_new()
{
    if (!xarVarFetch('contactid',     'id',     $contactid)) return;
    if (!xarVarFetch('returnurl',     'str::',     $returnurl,     $contactid)) return;

    if (!xarModAPILoad('dossier', 'user')) return;
    
    $data = xarModAPIFunc('dossier','admin','menu');

    if (!xarSecurityCheck('UseDossierReminders')) {
        return;
    }

    $item = xarModAPIFunc('dossier',
                          'user',
                          'get',
                          array('contactid' => $contactid));

    $data['authid'] = xarSecGenAuthKey();
    $data['contactid'] = $contactid;
    $data['returnurl'] = $returnurl;
    $data['item'] = $item;

    $data['addbutton'] = xarVarPrepForDisplay(xarML('Add Reminder'));

    return $data;
}

?>

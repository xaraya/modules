<?php

function dossier_reminders_modify($args)
{
    extract($args);
    
    if (!xarVarFetch('reminderid',     'id',     $reminderid,     $reminderid,     XARVAR_NOT_REQUIRED)) return;
    
    $item = xarModAPIFunc('dossier',
                         'reminders',
                         'get',
                         array('reminderid' => $reminderid));
    
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('UseReminders', 1, 'Reminders', "All:All:All:All")) {
        return;
    }

    $contactinfo = xarModAPIFunc('dossier',
                          'user',
                          'get',
                          array('contactid' => $item['contactid']));
    
    $data = xarModAPIFunc('dossier','admin','menu');
    
    $data['reminderid'] = $item['reminderid'];
    
    $data['authid'] = xarSecGenAuthKey();
    
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update'));

    $data['item'] = $item;
    
    $data['contactinfo'] = $contactinfo;
    
    return $data;
}

?>

<?php

function dossier_reminders_update($args)
{
    extract($args);
    
    if (!xarVarFetch('reminderid', 'id', $reminderid)) return;
    if (!xarVarFetch('ownerid', 'int::', $ownerid, $ownerid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('reminderdate', 'str::', $reminderdate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('notes', 'str::', $notes, '', XARVAR_NOT_REQUIRED)) return;
                                        
    if (!xarSecConfirmAuthKey()) return;
    
    $reminderinfo = xarModAPIFunc('dossier', 'reminders', 'get', array('reminderid' => $reminderid));
    
    if(!xarModAPIFunc('dossier',
                    'reminders',
                    'update',
                    array('reminderid'      => $reminderid,
                        'ownerid'           => $ownerid,
                        'reminderdate'      => $reminderdate,
                        'notes'             => $notes))) {
        return;
    }


    xarSessionSetVar('statusmsg', xarML('Reminder Updated'));

    xarResponseRedirect(xarModURL('dossier', 'admin', 'display', array('contactid' => $reminderinfo['contactid'], 'mode' => "reminders")));

    return true;
}

?>

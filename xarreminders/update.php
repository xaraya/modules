<?php

function xtasks_reminders_update($args)
{
    extract($args);
    
    if (!xarVarFetch('reminderid', 'id', $reminderid)) return;
    if (!xarVarFetch('taskid', 'int', $taskid, $taskid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ownerid', 'int::', $ownerid, $ownerid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('eventdate', 'str::', $eventdate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('reminder', 'str::', $reminder, '', XARVAR_NOT_REQUIRED)) return;
                                        
    if (!xarSecConfirmAuthKey()) return;
    
    if(!xarModAPIFunc('xtasks',
                    'reminders',
                    'update',
                    array('reminderid'            => $reminderid,
                        'ownerid'            => $ownerid,
                        'eventdate'       => $eventdate,
                        'reminder'          => $reminder))) {
        return;
    }


    xarSessionSetVar('statusmsg', xarML('Page Updated'));

    xarResponseRedirect(xarModURL('xtasks', 'admin', 'display', array('taskid' => $taskid, 'mode' => "reminders")));

    return true;
}

?>
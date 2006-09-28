<?php

function xtasks_reminders_create($args)
{
    extract($args);
    
    if (!xarVarFetch('taskid', 'id', $taskid, $taskid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('eventdate', 'str::', $eventdate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ownerid', 'int::', $ownerid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('reminder', 'str::', $reminder, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('relativeurl', 'str::', $relativeurl, '', XARVAR_NOT_REQUIRED)) return;
                                            
    if (!xarSecConfirmAuthKey()) return;

    $reminderid = xarModAPIFunc('xtasks',
                        'reminders',
                        'create',
                        array('taskid'          => $taskid,
                            'eventdate'         => $eventdate,
                            'ownerid'              => $ownerid,
                            'reminder'          => $reminder));


    if (!isset($reminderid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    xarSessionSetVar('statusmsg', xarMLByKey('REMINDERCREATED'));

    xarResponseRedirect(xarModURL('xtasks', 'admin', 'display', array('taskid' => $taskid, 'mode' => "reminders")));

    return true;
}

?>
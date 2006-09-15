<?php

function xtasks_worklog_create($args)
{
    extract($args);
    
    if (!xarVarFetch('taskid', 'id', $taskid, $taskid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ownerid', 'id', $ownerid, $ownerid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('eventdate', 'str::', $eventdate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hours', 'str::', $hours, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('notes', 'str::', $notes, '', XARVAR_NOT_REQUIRED)) return;
                                            
    if (!xarSecConfirmAuthKey()) return;

    $worklogid = xarModAPIFunc('xtasks',
                        'worklog',
                        'create',
                        array('taskid' 	    => $taskid,
                            'ownerid'       => $ownerid,
                            'eventdate'	    => $eventdate,
                            'hours'         => $hours,
                            'notes'  	    => $notes));


    if (!isset($worklogid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    xarSessionSetVar('statusmsg', xarMLByKey('WORKLOGCREATED'));

    xarResponseRedirect(xarModURL('xtasks', 'admin', 'display', array('taskid' => $taskid, 'mode' => "worklog")));

    return true;
}

?>
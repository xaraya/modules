<?php

function xtasks_worklog_update($args)
{
    extract($args);
    
    if (!xarVarFetch('worklogid', 'id', $worklogid)) return;
    if (!xarVarFetch('eventdate', 'str:1:', $eventdate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hours', 'int::', $hours, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('notes', 'str::', $notes, '', XARVAR_NOT_REQUIRED)) return;
                                        
    if (!xarSecConfirmAuthKey()) return;
    
    $worklog = xarModAPIFunc('xtasks', 'worklog', 'get', array('worklogid' => $worklogid));
    
    if(!$worklog) return;
    
    if(!xarModAPIFunc('xtasks',
					'worklog',
					'update',
					array('worklogid'	=> $worklogid,
						'eventdate'	    => $eventdate,
                        'hours'         => $hours,
                        'notes'  	    => $notes))) {
		return;
	}


	xarSessionSetVar('statusmsg', xarML('Work Record Updated'));

    xarResponseRedirect(xarModURL('xtasks', 'admin', 'display', array('taskid' => $worklog['taskid'], 'mode' => "worklog")));

    return true;
}

?>
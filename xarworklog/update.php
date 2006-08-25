<?php

function xtasks_worklog_update($args)
{
    extract($args);
    
    if (!xarVarFetch('reminderid', 'id', $reminderid)) return;
    if (!xarVarFetch('reminder_name', 'str:1:', $reminder_name, $reminder_name, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('projectid', 'id', $projectid, $projectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('status', 'str::', $status, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sequence', 'int::', $sequence, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('description', 'str::', $description, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('relativeurl', 'str::', $relativeurl, '', XARVAR_NOT_REQUIRED)) return;
                                        
    if (!xarSecConfirmAuthKey()) return;
    
    if(!xarModAPIFunc('xtasks',
					'reminders',
					'update',
					array('reminderid'	        => $reminderid,
						'reminder_name' 	    => $reminder_name,
                        'status'	        => $status,
                        'description'       => $description,
                        'relativeurl'  	    => $relativeurl))) {
		return;
	}


	xarSessionSetVar('statusmsg', xarML('Page Updated'));

    xarResponseRedirect(xarModURL('xtasks', 'admin', 'display', array('projectid' => $projectid, 'mode' => "reminders")));

    return true;
}

?>
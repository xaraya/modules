<?php

function xtasks_worklog_create($args)
{
    extract($args);
    
    if (!xarVarFetch('reminder_name', 'str:1:', $reminder_name, $reminder_name, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('projectid', 'id', $projectid, $projectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('status', 'str::', $status, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sequence', 'int::', $sequence, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('description', 'str::', $description, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('relativeurl', 'str::', $relativeurl, '', XARVAR_NOT_REQUIRED)) return;
                                            
    if (!xarSecConfirmAuthKey()) return;

    $reminderid = xarModAPIFunc('xtasks',
                        'reminders',
                        'create',
                        array('reminder_name' 	    => $reminder_name,
                            'projectid'         => $projectid,
                            'status'	        => $status,
                            'sequence'	        => $sequence,
                            'description'       => $description,
                            'relativeurl'  	    => $relativeurl));


    if (!isset($reminderid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    xarSessionSetVar('statusmsg', xarMLByKey('PROJECTCREATED'));

    xarResponseRedirect(xarModURL('xtasks', 'admin', 'display', array('projectid' => $projectid, 'mode' => "reminders")));

    return true;
}

?>
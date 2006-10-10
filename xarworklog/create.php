<?php

function xtasks_worklog_create($args)
{
    extract($args);
    
    if (!xarVarFetch('taskid', 'id', $taskid, $taskid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ownerid', 'id', $ownerid, $ownerid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('eventdate', 'str::', $eventdate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hours', 'str::', $hours, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hours_remaining', 'str::', $hours_remaining, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('notes', 'str::', $notes, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('close', 'str::', $close, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str::', $returnurl, '', XARVAR_NOT_REQUIRED)) return;
                                            
    if (!xarSecConfirmAuthKey()) return;

    $worklogid = xarModAPIFunc('xtasks',
                        'worklog',
                        'create',
                        array('taskid'          => $taskid,
                            'ownerid'           => $ownerid,
                            'eventdate'         => $eventdate,
                            'hours'             => $hours,
                            'hours_remaining'   => $hours_remaining,
                            'notes'             => $notes));


    if (!isset($worklogid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    xarSessionSetVar('statusmsg', xarMLByKey('WORKLOGCREATED'));
    
    if($close) { // affects dates only
        $taskinfo = xarModAPIFunc('xtasks', 'user', 'get', array('taskid' => $taskid));
        if(!$taskinfo && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
        $taskinfo['date_end_actual'] = $eventdate;
        $taskinfo['status'] = "Closed";
        if(!xarModAPIFunc('xtasks', 'admin', 'update', $taskinfo)) return;
        if($taskinfo['parentid'] > 0) { // must also check if any other open tasks to account for first
            $alltasksclosed = true;
            $siblings = xarModAPIFunc('xtasks', 'user', 'getall', array('parentid' => $taskinfo['parentid']));
            foreach($siblings as $childtask) {
                if($childtask['Status'] == "Open") $alltasksclosed = false;
            }
            if($alltasksclosed) {
                xarResponseRedirect(xarModURL('xtasks', 'admin', 'delete', array('taskid' => $taskinfo['parentid'])));
                return true;
            }
        }
    }
    
    if(empty($returnurl)) {
        $returnurl = xarModURL('xtasks', 'admin', 'display', array('taskid' => $taskid, 'mode' => "worklog"));
    }

    xarResponseRedirect($returnurl);

    return true;
}

?>
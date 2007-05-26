<?php

function xtasks_worklog_create($args)
{
    extract($args);
    
    if (!xarVarFetch('taskid', 'id', $taskid, $taskid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('taskownerid', 'id', $taskownerid, $taskownerid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ownerid', 'id', $ownerid, $ownerid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('eventdate', 'str::', $eventdate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hours', 'float::', $hours, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hours_remaining', 'float::', $hours_remaining, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('notes', 'str::', $notes, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('taskstatus', 'str::', $taskstatus, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str::', $returnurl, '', XARVAR_NOT_REQUIRED)) return;
                                            
    if (!xarSecConfirmAuthKey()) return;

    $worklogid = xarModAPIFunc('xtasks',
                        'worklog',
                        'create',
                        array('taskid'          => $taskid,
                            'ownerid'           => $ownerid,
                            'taskownerid'       => $taskownerid,
                            'eventdate'         => $eventdate,
                            'hours'             => $hours,
                            'hours_remaining'   => $hours_remaining,
                            'notes'             => $notes));


    if (!isset($worklogid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    xarSessionSetVar('statusmsg', xarMLByKey('WORKLOGCREATED'));
    
    $taskinfo = xarModAPIFunc('xtasks', 'user', 'get', array('taskid' => $taskid));
    if(!$taskinfo && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
    if($taskstatus) { // affects dates only
        $taskinfo['date_end_actual'] = $eventdate;
        $taskinfo['status'] = $taskstatus;
        
        if(!xarModAPIFunc('xtasks', 'admin', 'update', $taskinfo)) return;
        if($taskstatus == "Closed" && $taskinfo['parentid'] > 0) { // must also check if any other open tasks to account for first
            $alltasksclosed = true;
            $siblings = xarModAPIFunc('xtasks', 'user', 'getall', array('parentid' => $taskinfo['parentid']));
            foreach($siblings as $childtask) {
                if($childtask['status'] != "Closed") $alltasksclosed = false;
            }
            if($alltasksclosed) {
                xarResponseRedirect(xarModURL('xtasks', 'admin', 'delete', array('taskid' => $taskinfo['parentid'])));
                return true;
            }
        }
    
        if($taskinfo['owner'] != $taskownerid) {
            if(!xarModAPIFunc('xtasks',
                            'admin',
                            'updateassign',
                            array('taskid'              => $taskid,
                                'date_end_planned'      => $taskinfo['date_end_planned'],
                                'description'           => $taskinfo['description'],
                                'owner'                 => $taskownerid))) {
                return;
            }
        }
    }
    
    if($taskinfo['projectid'] > 0) {
        $teamlist = xarModAPIFunc('xproject', 'team', 'getall', array('projectid' => $taskinfo['projectid']));
        foreach($teamlist as $memberinfo) {
            xarModAPIFunc('xtasks', 'user', 'notify', array('contacttype' => 735, 'owner' => $memberinfo['memberid'], 'worklogid' => $worklogid, 'action' => ($close ? "CLOSED" : "PROGRESS")));
        }
    }
    
    if(empty($returnurl)) {
        $returnurl = xarModURL('xtasks', 'admin', 'display', array('taskid' => $taskid, 'mode' => "worklog"));
    }

    xarResponseRedirect($returnurl);

    return true;
}

?>
<?php

function xtasks_worklog_create($args)
{
    extract($args);
    
    if (!xarVarFetch('taskid', 'id', $taskid, $taskid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('taskownerid', 'id', $taskownerid, $taskownerid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ownerid', 'id', $ownerid, $ownerid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('eventdate', 'str::', $eventdate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hours', 'float::', $hours, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('status', 'str::', $status, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hours_remaining', 'float::', $hours_remaining, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('date_end_planned', 'isset::', $date_end_planned, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('notes', 'str::', $notes, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('close', 'str::', $close, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str::', $returnurl, '', XARVAR_NOT_REQUIRED)) return;
                                            
    if (!xarSecConfirmAuthKey()) return;
    
    if(!empty($eventdate)) {
        if (!preg_match('/[a-zA-Z]+/',$eventdate)) {
            $eventdate .= ' GMT';
        }
        $eventdate = strtotime($eventdate);
        if ($eventdate === false) $eventdate = -1;
        if ($eventdate >= 0) {
            // adjust for the user's timezone offset
            $eventdate -= xarMLS_userOffset($eventdate) * 3600;
        }
        $eventdate = date("Y-m-d H:i:s", $eventdate);
    }
    
    if(!empty($date_end_planned)) {
        if (!preg_match('/[a-zA-Z]+/',$date_end_planned)) {
            $date_end_planned .= ' GMT';
        }
        $date_end_planned = strtotime($date_end_planned);
        if ($date_end_planned === false) $date_end_planned = -1;
        if ($date_end_planned >= 0) {
            // adjust for the user's timezone offset
            $date_end_planned -= xarMLS_userOffset($date_end_planned) * 3600;
        }
        $date_end_planned = date("Y-m-d", $date_end_planned);
    }

    if($hours > 0 || !empty($notes) || $close) {
        
        if(empty($notes) && $close) $notes = xarML('Closing Task');
        
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
    } else {
        $worklogid = 0;
    }
    
    // continue to apply task updates regardless of worklog creation    
    $taskinfo = xarModAPIFunc('xtasks', 'user', 'get', array('taskid' => $taskid));
    if(!$taskinfo && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
    if(empty($status)) $status = $taskinfo['status'];
    
    $updatetask = false;
    
    if($close == true) $updatetask = true;
    
    if(!empty($status) && $status != $taskinfo['status']) $updatetask = true;
    
    if(!empty($taskownerid) && $taskownerid != $taskinfo['owner']) $updatetask = true;
    
    
    if($updatetask) { // affects dates only

        if($taskinfo['date_end_planned'] != $date_end_planned) {
            $taskinfo['date_end_planned'] = $date_end_planned;
        }
        
        $taskinfo['status'] = $close ? "Closed" : $status;
        if($taskinfo['status'] == "Closed") $taskinfo['date_end_actual'] = $eventdate;
        
        if(!xarModAPIFunc('xtasks', 'admin', 'update', $taskinfo)) return;
        if($taskinfo['parentid'] > 0) { // must also check if any other open tasks to account for first
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
    
        $projectinfo = xarModAPIFunc('xproject', 'user', 'get', array('projectid' => $taskinfo['projectid']));
        if($projectinfo == false) return;
        
        if($date_end_planned > $projectinfo['planned_end_date']) {
            $projectinfo['planned_end_date'] = $date_end_planned;
            if(!xarModAPIFunc('xproject', 'admin', 'update',$projectinfo)) return;
        }
    
        $teamlist = xarModAPIFunc('xproject', 'team', 'getall', array('projectid' => $taskinfo['projectid']));
        foreach($teamlist as $memberinfo) {
            xarModAPIFunc('xtasks', 'user', 'notify', array('contacttype' => 779, 'owner' => $memberinfo['memberid'], 'worklogid' => $worklogid, 'action' => ($close ? "CLOSED" : "PROGRESS")));
        }
    }
    
    if(empty($returnurl)) {
        $returnurl = xarModURL('xtasks', 'admin', 'display', array('taskid' => $taskid, 'mode' => "worklog"));
    }

    xarResponseRedirect($returnurl);

    return true;
}

?>
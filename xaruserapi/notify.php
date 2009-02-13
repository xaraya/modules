<?php

function xtasks_userapi_notify($args)
{
    extract($args);
    
    if(!$owner) return;
    if(!isset($taskid) && !isset($worklogid) && !isset($projectid)) return;
    if(!isset($contacttype)) $contacttype = 779;
    if(!isset($taskid)) $taskid = 0;
    if(!isset($worklogid)) $worklogid = 0;
    if(!isset($projectid)) $projectid = 0;
    if(!isset($isowner)) $isowner = false;
    
    if($taskid > 0) $component = "TASK";
    elseif($projectid > 0) $component = "PROJECT";
    elseif($worklogid > 0) $component = "WORK";
    
    $from_email = xarModGetVar('julian','from_email');
    $from_name =  xarModGetVar('julian','from_name');
    
    if($worklogid > 0) {
        $workloginfo = xarModAPIFunc('xtasks', 'worklog', 'get', array('worklogid' => $worklogid));
        if($workloginfo == false) return;
        $taskid = $workloginfo['taskid'];
    }
    
    if($taskid > 0) {
        $taskinfo = xarModAPIFunc('xtasks', 'user', 'get', array('taskid' => $taskid));
        if($taskinfo == false) return;
        $projectid = $taskinfo['projectid'];
    }
        
    if($projectid > 0) {
        $projectinfo = xarModAPIFunc('xproject', 'user', 'get', array('projectid' => $projectid));
        if($projectinfo == false) return;
    }
        
    if(empty($taskinfo['task_name'])) $taskinfo['task_name'] = "<no task name>";
    
    $projectinfo = array();
    if(isset($taskinfo['projectid']) && $taskinfo['projectid'] > 0) {
        $projectinfo = xarModAPIFunc('xproject', 'user', 'get', array('projectid' => $taskinfo['projectid']));
    }
    if($projectinfo == false) return;
    
    // need to determine the module that the contact field uses: userlist, addressbook, crm, or dossier
    // default to userlist
    switch($contacttype) {
        case 735: // addressbook
            break; // not used anymore
            $ownerinfo = xarModAPIFunc('addressbook', 'user', 'getdetailvalues', array('id' => $owner));
            // send mail with mail module
            switch($ownerinfo['c_main']) {
                case 0:
                    $member_email = $ownerinfo['contact_1'];
                    break;
                case 1:    
                    $member_email = $ownerinfo['contact_2'];
                    break;
                case 2:    
                    $member_email = $ownerinfo['contact_3'];
                    break;
                case 3:    
                    $member_email = $ownerinfo['contact_4'];
                    break;
                case 4:    
                    $member_email = $ownerinfo['contact_5'];
                    break;
            }
            if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $member_email)) {
        //        $member_email = $member_email;
            } elseif(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $ownerinfo['contact_1'])) {
                $member_email = $ownerinfo['contact_1'];
            } elseif(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $ownerinfo['contact_2'])) {
                $member_email = $ownerinfo['contact_2'];
            } elseif(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $ownerinfo['contact_3'])) {
                $member_email = $ownerinfo['contact_3'];
            } elseif(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $ownerinfo['contact_4'])) {
                $member_email = $ownerinfo['contact_4'];
            } elseif(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $ownerinfo['contact_5'])) {
                $member_email = $ownerinfo['contact_5'];
            }
            break;
            
        case 779: // dossier
            $ownerinfo = xarModAPIFunc('dossier', 'user', 'get', array('contactid' => $owner));
            if($ownerinfo == false) return;
            $member_email = "";
            if(!empty($ownerinfo['email_1'])) $member_email = $ownerinfo['email_1'];
            elseif(!empty($ownerinfo['email_2'])) $member_email = $ownerinfo['email_2'];
//            else mail("chad@mindsmack.com", "contact creation test for: ".$owner, "contact creation test for: ".$owner);
            if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $member_email)) {
                return;
            }
            break;
               
        default:
            if($ownerinfo = xarModAPIFunc('roles','user','get',array('uid'=>$owner))) {
                $member_email = xarUserGetVar('email', $owner);
            }
            break;
    }
    
    if(!isset($member_email)) $member_email = "";

    $messagetext = "Project: ".$projectinfo['project_name']." ("
                    ."<a href='".xarModURL('xproject', 'admin', 'display', array('projectid' => $projectid))."&amp;mode=tasks'>click here</a>)";
    if($component == "TASK" || $component == "WORK") {
        $messagetext .= "<hr>Task: ".$taskinfo['task_name']."\n"
                    .$taskinfo['formatted_desc']."\n"
                    .($taskinfo['date_end_planned'] ? "Due: ".$taskinfo['date_end_planned'] : "");
    }
    if($component == "WORK") {
        $messagetext .= "<hr>Work Performed: ".$workloginfo['formatted_notes'];
    }
    
    switch($action) {
        case "CREATE":
            if($isowner) {
                $message = "This ".$component." has been created and assigned to you.\n".$messagetext;
            } else {
                $message = "This ".$component." has been created.\n".$messagetext;
            }
            break;
        
        case "UPDATE":
            if($isowner) {
                $message = "This ".$component." has been updated and is assigned to you.\n".$messagetext;
            } else {
                $message = "This ".$component." has been updated.\n".$messagetext;
            }
            break;
        
        case "ASSIGN":
            if($isowner) {
                $message = "This ".$component." has been assigned to you.\n".$messagetext;
            } else {
                $message = "This ".$component." has been assigned.\n".$messagetext;
            }
            break;
        
        case "DELETE":
            if($isowner) {
                $message = "This ".$component.", assigned to you, has been deleted.\n".$messagetext;
            } else {
                $message = "This ".$component." has been deleted.\n".$messagetext;
            }
            break;
        
        case "CLOSED":
            if($isowner) {
                $message = "This ".$component." has been closed by someone else.\n".$messagetext;
            } else {
                $message = "This ".$component." has been closed.\n".$messagetext;
            }
            break;
        
        case "HOURS":
            if($isowner) {
                $message = "New hours have been reported for a ".$component." assigned to you.\n".$messagetext;
            } else {
                $message = "New hours have been reported for a ".$component.".\n".$messagetext;
            }
            break;
        
        case "PRIORITY":
            if($isowner) {
                $message = "A ".$component." assigned to you has been reprioritized.\n".$messagetext;
            } else {
                $message = "A ".$component." has been reprioritized.\n".$messagetext;
            }
            break;
        
        case "IMPORTANCE":
            if($isowner) {
                $message = "A ".$component." assigned to you has changed in importance.\n".$messagetext;
            } else {
                $message = "A ".$component." has changed in importance.\n".$messagetext;
            }
            break;
        
        case "PROGRESS":
            if($isowner) {
                $message = "A ".$component." assigned to you has changed in importance.\n".$messagetext;
            } else {
                $message = "Progress has been reported for a task.\n".$messagetext;
            }
            break;
        
    }
//    if($member_email == "myself@xaraya.com") die("test: ".serialize($ownerinfo));
//        if(eregi("^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$", $member_email)) {
    if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $member_email)) {
        if (!xarModAPIFunc('mail', 'admin', 'sendhtmlmail',
                 array('from'        => $from_email,
                       'fromname'    => $from_name,
                       'info'        => $member_email,
                       'subject'     => xarML('xTasks Alert').": ".$component." ".$action." - ".$projectinfo['project_name'],
                       'message'     => $message,
                       'htmlmessage' => nl2br($message),
                       'wordwrap'    => 80 ))) {
            return;
        }
    }
    
}

?>
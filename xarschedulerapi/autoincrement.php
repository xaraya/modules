<?php
/**
 * Emails alerts
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @copyright (C) 2004 by Metrostat Technologies, Inc.
 */
 
/*
 * Emails alerts re: events to the user based on which categories the user has selected to recieve.
 * This script is intended to be run via the scheduler module. It should be run once a day.
 *
 * initial template: Roger Raymond
 * @author Jodie Razdrh/John Kevlin/David St.Clair
 * @link http://www.metrostat.net
 * 
 * @access private
 *
 * @ TODO MichelV <1> Generate cleaner mail function to incorporate templates.
 */
function xtasks_schedulerapi_autoincrement($args)
{
    extract ($args);
    
    $activestatus = xarModGetVar('xproject', 'activestatus');

    $projectlist = xarModAPIFunc('xproject', 'user', 'getall', array('status' => $activestatus));
    
    $feedback = "";
    $usertasks = array();
    foreach($projectlist as $projectinfo) {
        $tasklist = xarModAPIFunc('xproject', 'user', 'getall', array('status' => "Active"));
        $ttl_priority = 0;
        $ttl_tasks = 0;
        $increment_priority = true;
        foreach($tasklist as $taskinfo) {
            $ttl_priority = $ttlpriority + $taskinfo['priority'];            
            $ttl_tasks++;
            if($taskinfo['priority'] == 1) $increment_priority = false;

            // compile list of active tasks for each user
            $usertasks[$taskinfo['ownerid']][$taskinfo['projectid']][$taskinfo['taskid']] = $taskinfo;
            $projectdata[$projectinfo['projectid']] = $projectinfo;
        }
        $avg_priority = (int)($ttl_priority / $ttl_tasks);
        if($avg_priority != $projectinfo['priority']) {
            $feedback .= "project priority changed from ".$projectinfo['priority']." to ".$avg_priority."\n\n";
            $projectinfo['priority'] = $avg_priority;
            xarModAPIFunc('xproject', 'admin', 'update', $projectinfo);
        }
        if($increment_priority) {
            foreach($tasklist as $taskinfo) {
                $feedback .= "task priority changed from ".$taskinfo['priority']." to ".($taskinfo['priority'] + 1)."\n\n";
                $taskinfo['priority'] += 1;
                xarModAPIFunc('xtasks', 'admin', 'update', $taskinfo);
            }
        }     
    
    }
    
    foreach($usertasks as $ownerid => $projectlist) {
        $messagetext = $feedback;
        foreach($projectlist as $projectid => $tasklist) {
            $messagetext .= "\n\n\nProject: ".$projectinfo['project_name']."\n"
                            .xarModURL('xproject', 'admin', 'display', array('projectid' => $projectid))."\n\n"
            foreach($tasklist as $taskinfo) {
    
                $messagetext .= "Task: ".$taskinfo['task_name']."\n"
                                .xarModURL('xtasks', 'admin', 'display', array('taskid' => $taskid))."\n\n"
                                .$taskinfo['description']."\n\n"
                                .($taskinfo['date_end_planned'] ? "Due: ".$taskinfo['date_end_planned']."\n" : "");
                            
            
            }
        }
                
        // fixme: must determine if ownerid field is a userlist field or a contactlist field!!!

        if (!xarModAPIFunc('mail', 'admin', 'sendmail',
                 array('from'        => "testing@miragelab.com",
                       'fromname'    => "labXarTesting,
                       'info'        => xarUserGetVar('email', $ownerid),
                       'subject'     => xarML('Auto-Increment Priorities Alert'),
                       'message'     => "Stuff that changed: \n\n".$messagetext))) {
            return;
        }
    }
    
    
    return 1;
} 
?>

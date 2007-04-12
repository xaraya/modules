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
function xtasks_schedulerapi_emailreminders($args)
{
    extract ($args);
    
            if (!xarModAPIFunc('mail', 'admin', 'sendmail',
                     array('from'        => "testing@miragelab.com",
                           'fromname'    => "labXarTesting,
                           'info'        => xarUserGetVar('email'),
                           'subject'     => xarML('Event Alert'),
                           'message'     => "test test test"))) {
                return;
            }
            return 1;
    
    if(!xarModLoad('addressbook', 'user')) return;
    xarModAPILoad('addressbook', 'user');
    
    // TODO possibility to configure when alerts are send (1 day before?, 1 week?)
    //get tomorrow's events
    $startdate = date("Y-m-d",strtotime("today"));
    $enddate = date("Y-m-d",strtotime("tomorrow"));
    
    $from_email = xarModGetVar('julian','from_email');
    $from_name =  xarModGetVar('julian','from_name');

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $reminderstable = $xartable['xtasks_reminders'];

    $sql = "SELECT reminderid,
                  taskid,
                  ownerid,
                  eventdate,
                  reminder
            FROM $reminderstable
            WHERE eventdate > '".$startdate."'
            AND eventdate < '".$enddate."'
            ORDER BY eventdate";

    $result = $dbconn->Execute($sql);

    if (!$result) return;
    
    $alertevents = array();

    for (; !$result->EOF; $result->MoveNext()) {
        list($reminderid,
              $taskid,
              $ownerid,
              $eventdate,
              $reminder) = $result->fields;
        
        $taskinfo = xarModAPIFunc('xtasks', 'user', 'get', array('taskid' => $taskid));

        if(isset($taskinfo) && $taskinfo['projectid'] > 0) {
            $projectinfo = xarModAPIFunc('xproject', 'user', 'get', array('projectid' => $taskinfo['projectid']));
        } else {
            $projectinfo = array();
        }
        
        if(isset($projectinfo) && $projectinfo['clientid'] > 0) {
            $clientinfo = xarModAPIFunc('addressbook', 'user', 'getdetailvalues', array('id' => $projectinfo['clientid']));
        } else {
            $clientinfo = array();
        }
        
        $alertevents[] = array('reminderid'     => $reminderid,
                              'taskid'          => $taskid,
                              'taskinfo'        => $taskinfo,
                              'projectinfo'     => $projectinfo,
                              'clientinfo'      => $clientinfo,
                              'ownerid'         => $ownerid,
                              'eventdate'       => $eventdate,
                              'reminder'        => $reminder);
    }

    $result->Close();
        
    if (is_array($alertevents)) {
        //send mail
        
        // TODO; creation of message in a template?
        $message = "The following events are scheduled for ".date('m-d-Y',strtotime($startdate)).":";
        $htmlmessage  = $message."<br /><br />";
        $txtmessage = $message."\n\n";
        foreach ($alertevents as $event) {
            //html message
            $htmlmessage .=
                $event['clientinfo']['company'].'<br />'.
                $event['projectinfo']['project_name'].'<br />'.
                $event['eventdate'].' <a href="'.
                xarModURL('xtasks','admin','display',
                          array(
                              'taskid' => $event['taskid']
                         )).
                '">'.$event['taskinfo']['task_name'].'</a><br />'.
                '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$event['taskinfo']['description'].'<br />';          
            //text message
            $txtmessage .=
                $event['clientinfo']['sortname']."\n".
                $event['projectinfo']['project_name']."\n".
                $event['eventdate']." ".$event['taskinfo']['task_name']."\n".
                "     ".$event['taskinfo']['description']."\n"; 
        
            // send mail with mail module
            if (!xarModAPIFunc('mail', 'admin', 'sendmail',
                     array('from'        => $from_email,
                           'fromname'    => $from_name,
                           'info'        => xarUserGetVar('email', $event['ownerid']),
                           'subject'     => xarML('Event Alert'),
                           'message'     => $txtmessage,
                           'htmlmessage' => $htmlmessage))) {
                return;
            }
        }
    }
    
    return 1;
} 
?>

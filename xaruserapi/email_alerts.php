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

/**
 * Emails alerts re: events to the user based on which categories the user has selected to recieve.
 * This script is intended to be run via the scheduler module. It should be run once a day.
 *
 * initial template: Roger Raymond
 * @author Jodie Razdrh/John Kevlin/David St.Clair
 * @link http://www.metrostat.net
 *
 * @TODO MichelV <1> Generate cleaner function to incorporate templates.
 * @Legacy This function is moved to xarschedulerapi for nicer compatibility
 */
function julian_userapi_email_alerts()
{
    //load the calendar class
    $c = xarModAPIFunc('julian','user','factory','calendar');
    // TODO possibility to configure when alerts are send (1 day before?, 1 week?)
    //get tomorrow's events
    $startdate = date("Y-m-d",strtotime("tomorrow"));
    // get events where to send alertmails for.
    $events = array();
    // get all the events from tomorrow to tomorrow
    $events = xarModApiFunc('julian','user','getall', array('startdate'=>$startdate, 'enddate'=>$startdate));
    // get all subscriptions per user
    $allsubscriptions = xarModAPIFunc('julian','user','getallsubcriptions');

    //set the from email address and name. These variables are configurable.
    $from_email = xarModGetVar('julian','from_email');
    $from_name =  xarModGetVar('julian','from_name');

    //For each user
    foreach ($allsubscriptions as $uid => $subscriptions)
    {
        $alertevents = array();

        //For each day
        foreach ($events as $key=>$val)
        {
            //For each event in that day
            foreach ($events[$key] as $key2=>$val2)
            {
                //see if the user requested an alert be sent for this type event
                if (in_array($events[$key][$key2]['categories'],$subscriptions))
                {
                    //only mail this event if it is the user's event or it is not the user's event and is a public event
                    if(!strcmp($events[$key][$key2]['organizer'],$row->uid) || (strcmp($events[$key][$key2]['organizer'],$row->uid) && !$events[$key][$key2]['class']))
                    {
                        $alertevents[] = $events[$key][$key2];
                    }
                }
            }
        }

        if (!empty($alertevents)) {
            //send mail

            // TODO; creation of message in a template?
            $message = "The following events are scheduled for ".date('m-d-Y',strtotime($startdate)).":";
            $txtmessage  = $message."<br /><br />";
            $htmlmessage = $message."\n\n";
            foreach ($alertevents as $event) {
                $time = strcmp($event['time'],'') ? $event['time'] : 'All Day Event:';
                //html message
                $htmlmessage .=
                    $time.' <a href="'.
                    xarModURL('julian','user','view',
                              array(
                                  'cal_date' => date("Ymd",strtotime("tomorrow")),
                                  'event_id' => $event['event_id']
                             )).
                    '">'.$event['summary'].'</a><br />'.
                    '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$event['description'].'<br />';
                //text message
                $txtmessage .=
                    $time." ".$event['summary']."\n".
                    "     ".$event['description']."\n";
            }

            // send mail with mail module
            if (!xarModAPIFunc('mail', 'admin', 'sendmail',
                     array('from'        => $from_email,
                           'fromname'    => $from_name,
                           'info'        => xarUserGetVar('email'),
                           'subject'     => xarML('Event Alert'),
                           'message'     => $txtmessage,
                           'htmlmessage' => $htmlmessage))) {
                return;
            }
        }

    }

    return 1;

    /*//replacement above
    //establish a db connection
    $dbconn = xarDBGetConn();
    //get db tables
    $xartable = xarDBGetTables();
    $roles_table  = $xartable['roles'];
    $alerts_table = $xartable['julian_alerts'];

    //load the calendar class
    $c = xarModAPIFunc('julian','user','factory','calendar');

    //get tomorrow's events
    $startdate = date("Y-m-d",strtotime("tomorrow"));
    $events    = $c->getEvents($startdate);
    //build header
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1";
    //set the from email address and name. These variables are configurable.
    $from_email = xarModGetVar('julian','from_email');
    $from_name =  xarModGetVar('julian','from_name');

    //begin the mail message content
    $message = "The following events are scheduled for ".date('m-d-Y',strtotime($startdate)).":";
    //determine who wants to be alerted re: calendar events and for which event categories
    $sql = "SELECT * FROM " . $alerts_table . " WHERE 1;";
    $rs = $dbconn->Execute($sql);
    $txtmessage = '';
    $htmlmessage = '';

    //For each user
    while (!$rs->EOF) {
       $hasMail = 0;
       $row = $rs->FetchObject(false);
       $sql2 = "SELECT xar_uid,xar_email FROM " . $roles_table . " WHERE xar_uid='".$row->uid."';";
       $rs2 = $dbconn->Execute($sql2);
       $row2 = $rs2->FetchObject(false);
       $to_email = $row2->xar_email;
       $subscriptions = unserialize($row->subscriptions);
       //For each day
       foreach ($events as $key=>$val)
       {
           //For each event in that day
           foreach ($events[$key] as $key2=>$val2)
           {
                //see if the user requested an alert be sent for this type event
               if (in_array($events[$key][$key2]['categories'],$subscriptions))
               {
                   //only mail this event if it is the user's event or it is not the user's event and is a public event
                   if(!strcmp($events[$key][$key2]['organizer'],$row->uid) || (strcmp($events[$key][$key2]['organizer'],$row->uid) && !$events[$key][$key2]['class']))
                   {
                       $time = strcmp($events[$key][$key2]['time'],'')?$events[$key][$key2]['time']:"All Day Event:";
                       //html message
                       $htmlmessage.= $time." <a href=\"".xarServerGetBaseURL()."/index.php?module=julian&func=view&cal_date=" . date("Ymd",strtotime("tomorrow"))."&event_id=".$events[$key][$key2]['event_id']."\">".$events[$key][$key2]['summary']."</a><br />";
                       $htmlmessage.= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$events[$key][$key2]['description']."<br />";
                       //text message
                       $txtmessage.= $time." ".$events[$key][$key2]['summary']."\n";
                       $txtmessage.= "     ".$events[$key][$key2]['description']."\n";
                       $hasMail = 1;
                   }
               }
           }
       }
       if ($hasMail) {
           $htmlmessage=$message."<br /><br />".$htmlmessage;
           $txtmessage=$message."\n\n".$txtmessage;
           //Send email via Xaraya Mail function.
           if (!xarModAPIFunc('mail', 'admin', 'sendhtmlmail',
                     array('from' => $from_email,
                             'info' => $to_email,
                            'fromname' =>$from_name,
                             'subject'=> "Event Alert",
                           'message'=> $txtmessage,
                             'htmlmessage'=> $htmlmessage
                            ))) return;
           $txtmessage = '';
           $htmlmessage='';
       }
       $rs->MoveNext();
    }
    return 1;
    */
}
?>

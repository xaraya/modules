<?php
/**
 * Emails alerts
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */
/**
 * Emails alerts re: events to the user based on which categories the user has selected to recieve.
 * This script is intended to be run via the scheduler module. It should be run once a day.
 *
 * initial template: Roger Raymond
 * @author Jodie Razdrh/John Kevlin/David St.Clair
 * @link http://www.metrostat.net
 * @copyright (C) 2004 by Metrostat Technologies, Inc.
 *
 * @access private
 * @return int 1
 * @TODO MichelV <1> Generate cleaner mail function to incorporate templates.
 */
function julian_userapi_email_alerts($args)
{
    extract ($args);

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
                           'info'        => xarUserGetVar('email', $uid),
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

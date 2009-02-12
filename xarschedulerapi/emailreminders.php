<?php
 
function dossier_schedulerapi_emailreminders($args)
{
    extract ($args);
    
    // TODO possibility to configure when alerts are send (1 day before?, 1 week?)
    //get tomorrow's events
//    $lastrun = xarModGetVar('scheduler', 'lastrun');  // useless, is always 1 minute ago...
    $startdate = date("Y-m-d H:i:s");
    $end_time = time() + (3600 * 24);
    $enddate = date("Y-m-d H:i:s",$end_time);
    
    $from_email = xarModGetVar('dossier','from_email');
    $from_name =  xarModGetVar('dossier','from_name');

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $reminderstable = $xartable['dossier_reminders'];

    $sql = "SELECT reminderid,
                  contactid,
                  ownerid,
                  reminderdate,
                  warningtime,
                  notes
            FROM $reminderstable
            WHERE reminderdate > ?
            AND reminderdate < ?
            ORDER BY reminderdate";

    $bindvars = array($startdate,$enddate);
            
    $result = $dbconn->Execute($sql,$bindvars);

    if (!$result) return;
    
    $alertevents = array();

    for (; !$result->EOF; $result->MoveNext()) {
        list($reminderid,
              $contactid,
              $ownerid,
              $reminderdate,
              $warningtime,
              $notes) = $result->fields;
        
        $clientinfo = xarModAPIFunc('dossier', 'user', 'get', array('contactid' => $contactid));
        
        $alertevents[] = array('reminderid'     => $reminderid,
                              'contactid'       => $contactid,
                              'clientinfo'      => $clientinfo,
                              'ownerid'         => $ownerid,
                              'reminderdate'    => $reminderdate,
                              'warningtime'     => $warningtime,
                              'notes'           => $notes);
    }

    $result->Close();
        
    if (is_array($alertevents)) {
        //send mail
        
        // TODO; creation of message in a template?
        foreach ($alertevents as $event) {
            $message = "The following event is scheduled for ".date('m-d-Y',strtotime($reminderdate)).":";
            $htmlmessage  = $message."<br /><br />";
            $txtmessage = $message."\n\n";
            $clientinfo = $event['clientinfo']; // xarModAPIFunc('dossier','user','get',array('contactid'=>$event['ownerid']));
            $eventowner_email = xarUserGetVar('email', $event['ownerid']);
            $eventowner_name = xarUserGetVar('name', $event['ownerid']);
            if(isset($clientinfo)) { // && !empty($clientinfo['email_1'])) {
                //html message
                $htmlmessage .=
                    $event['clientinfo']['company'].'<br />'.
                    $event['clientinfo']['sortname'].'<br />'.
                    $event['reminderdate'].'<br />'
                    .$event['notes'].'<br />';         
                //text message
                $txtmessage .=
                    $event['clientinfo']['company']."\n".
                    $event['clientinfo']['sortname']."\n".
                    $event['reminderdate']."\n".
                    $event['notes']."\n";         
            
                // send mail with mail module
                if (!xarModAPIFunc('mail', 'admin', 'sendmail',
                         array('from'        => $from_email,
                               'fromname'    => $from_name,
                               'info'        => $eventowner_email,
                               'subject'     => xarML('Contact Reminder Alert'),
                               'message'     => $txtmessage,
                               'htmlmessage' => $htmlmessage))) {
                    return;
                }
            }
        }
    }
    
    return 1;
} 
?>

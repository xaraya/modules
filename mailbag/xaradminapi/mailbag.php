<?php
/**
    Run the Mailbag
   
    @returns true if successful
             otherwise false
*/
function mailbag_adminapi_mailbag()
{
    require_once("modules/mailbag/xarincludes/Socket.php");
    require_once("modules/mailbag/xarincludes/POP3.php");

    $mailbagdebug = true;

    $msgindex = array();

    // Get blacklists
    $sblacklist = xarModAPIFunc('mailbag', 'admin', 'getsblacklist');
    $rblacklist = xarModAPIFunc('mailbag', 'admin', 'getrblacklist');
    $ublacklist = xarModAPIFunc('mailbag', 'admin', 'getublacklist');

    // Get Mail Lists
    $maillist = xarModAPIFunc('mailbag', 'admin', 'getmaillist');

    $pop = new Net_POP3();
    
    if($pop->connect(xarModGetVar('mailbag', 'popserver')))
    {
        $pop->login(xarModGetVar('mailbag', 'popuser'), xarModGetVar('mailbag', 'poppass'));

        /**
            Get Number of messages on the server
        */
        $msgcount = $pop->numMsg();
        if ($mailbagdebug) echo $msgcount." messages to be processed<br>";

        /**
            Lets Get all info on all Messages
        */
        for($i = 1; $i <= $msgcount; $i++)
        {
            $msgindex[$i] = $pop->getParsedHeaders($i);
            $msgindex[$i]['Text'] = $pop->getBody($i);
            $msgindex[$i]['Size'] = strlen($msgindex[$i]['Text']);
        }
        //echo var_dump($msgindex[2]);
        // ----------------------------------------------------------------------
        // Check MailBag error log for messages to be processed (errorcode = 99)
        // ----------------------------------------------------------------------
        $errcount = xarModAPIFunc('mailbag', 'admin', 'geterrors', 
                                  array('i' => $i, 'msgindex' => &$msgindex, 'errorcode' => 99));
        if ($mailbagdebug) echo $errcount." messages loaded<br>";
        
        /**
            Loop through all messages
        */
        foreach($msgindex as $key => $msg)
        {
            $mailerror = '';
            // ----------------------------------------------------------------------
            // Message processing: Sender (FROM)
            // ----------------------------------------------------------------------

            // Get sender from from or reply-to fields
            $emailfrom = '';
            if (!empty($msg['Reply-To'])) 
            {
                $match = eregi("([[:alnum:]\._=-]+)@([[:alnum:]\._-]+)\.([[:alpha:]]+)", $msg['Reply-To'], $from);
                $emailfrom = $from[0];
            } 
            elseif(!empty($msg['From'])) 
            {
                $match = eregi("([[:alnum:]\._=-]+)@([[:alnum:]\._-]+)\.([[:alpha:]]+)", $msg['From'], $from);
                $emailfrom = $from[0];
            } 
            else 
            {
                $mailerror = xarML('There is no Sender.');
            }
            
            if ($mailbagdebug) echo $mailerror;
            
            // ----------------------------------------------------------------------
            // Check sender blacklist
            // ----------------------------------------------------------------------
            if(!empty($sblacklist[$emailfrom]))
            {
                $mailerror = xarML("Mailbag Sender Blacklisted: ") . $emailfrom;
                mailbagreply($emailfrom, $mailerror, $msg['Text']);
            }
            
            if(!empty($emailfrom))
                $user = xarModAPIFunc('roles', 'user', 'get', array('email' => $emailfrom));
                
            if(!empty($user) && is_array($user))
            {
                $from_userid = $user['uid'];
                $from_uname = $user['uname'];
            }
            else
            {
                $user = xarModAPIFunc('roles', 'user', 'get', array('uname' => 'Anonymous'));
                $from_userid = $user['uid'];
                $from_uname = $user['uname'];
                //echo var_dump($user);
            }
            
            // ----------------------------------------------------------------------
            // Message processing: Recipients (TO, CC, BCC)
            // ----------------------------------------------------------------------

            $emailto = array();
            $match = array();

            if(!empty($msg['To']))
            {
                $to = explode(", ", $msg['To']);
                $j = 0;
                while( $j <= count($to) && count($emailto) < xarModGetVar('mailbag', 'maxrecip'))
                {
                    if (!empty($to[$j]) && eregi("([[:alnum:]\._=-]+)@(".xarModGetVar('mailbag', 'emaildomain').")", $to[$j], $match)) 
                    {
                        $emailto[] = $match[1];
                        $match = array();
                    }
                    $j++;
                }
            }
            
            if(!empty($msg['CC']))
            {
                $cc = explode(", ", $msg['CC']);
                $j = 0;
                while($j<=count($cc) && count($emailto) <= xarModGetVar('mailbag', 'maxrecip'))
                {
                    if (eregi("([[:alnum:]\._=-]+)@(".xarModGetVar('mailbag', 'emaildomain').")", $cc[$j], $match)) 
                    {
                        $emailto[] = $match[1];
                        $match = array();
                    }
                }
            }
            
            if(!empty($msg['BCC']))
            {        
                $bcc = explode(", ", $msg['BCC']);
                $j = 0;
                while($j<=count($cc) && count($emailto) <= xarModGetVar('mailbag', 'maxrecip'))
                {
                    if(eregi("([[:alnum:]\._=-]+)@(".xarModGetVar('mailbag', 'emaildomain').")", $bcc[$j], $match)) 
                    {
                        $emailto[] = $match[1];
                        $match = array();
                    }
                    $j++;
                }
            }
            if ($mailbagdebug) echo implode(", ", $emailto) . " ";

            // ----------------------------------------------------------------------
            // Message processing: Subject and size
            // ----------------------------------------------------------------------

            $subject = xarVarPrepForDisplay($msg['Subject']);
            if ($msg['Size'] > xarModGetVar('mailbag', 'maxsize'))
            {
                $mailerror = xarML("Mail is larger than ") . xarModGetVar('mailbag', 'maxsize') . xarML(" bytes");
                mailbagreply($emailfrom, $mailerror, $msg['Text']);
            }
            
            /**
                We may still need to decode the body of the Message
            */
            if(substr($msg['Content-Type'], 0, 10) != "text/plain")
            {
                $mailerror = "This messages is not encoded in plain text and we can not process it yet";
            }
            
            /**
                If there was no error, then it is time to route the message
            */
            if(!$mailerror)
            {
                if($mailbagdebug) 
                {
                    echo "<hr> Route message ".$key."<br>";
                    echo "Size: " . $msg['Size'] . "<br>";
                    echo "To: " . $msg['To'] . "<br>";
                    echo "From: " . $msg['From'] . "<br>";
                    echo "Subject: " . $msg['Subject'] . "<br>";
                    echo "Text: " . $msg['Text'] . "<hr>";
                }
                
                if (xarModGetVar('mailbag', 'senderemail') == '1' && 
                    $from_userid == '1' && $i <= $msgcount && $maillistid == "") 
                {
                    $msg['Text'] = xarML("From: ") . $emailfrom . "<br>\n<br>\n" . $msg['Text'];
                }
                $time = date("Y-m-d H:i", $msg['Date']);
                if(!$subject) $subject = xarML("Subject");
                if(!$msg['Text']) $msg['Text'] = xarML("Body");
              
                // ----------------------------------------------------------------------
                // Message routing: Determine where the message should go in Xaraya
                // In the following order: Module Func, News Category, Messages
                // ----------------------------------------------------------------------

                // Loop through all recipients
                if (count($emailto) == 0) {
                    $mailerror = xarML("No Recipient");
                    $mailbagerrorcode = 3;
                }

                for($j = 0; $j < count($emailto); $j++) 
                {
                    $to = $emailto[$j];
                
                    // ----------------------------------------------------------------------
                    // Check recipient blacklist
                    // ----------------------------------------------------------------------
                    if (!empty($rblacklist[$to])) {
                        $mailerror = xarML("Mailbag Recipients Blacklist: ") . 
                                     $to ."@" . xarModGetVar('mailbag', 'emaildomain');
                        mailbagreply($emailfrom, $mailerror, $msg['Text']);
                        continue;
                    }

                    $user = xarModAPIFunc('roles', 'user', 'get', array('email' => $msg['From']));
                    $to_userid = $user['uid'];
                    // ----------------------------------------------------------------------
                    // Check user id blacklist
                    // ----------------------------------------------------------------------
                    if (!empty($ublacklist[$to_userid])) {
                      $mailerror = xarML("Mailbag User Blacklist: ") .
                                    $to_userid." (".$to."@".xarModGetVar('mailbag', 'emaildomain').")";
                      mailbagreply($emailfrom, $mailerror, $msg['Text']);
                      continue;
                    }
                
                    
                
                
                
                
                }// End of recipient loop
                
                // ----------------------------------------------------------------------
                // Message routing: MAILBAG LOG
                // ----------------------------------------------------------------------
                if ($mailerror && $mailbagerrorcode) {
                /*
                    // Load message in error table
                    $dbconn   =& xarDBGetConn();
                    $xartable =  xarDBGetTables();
                    $table = $xartable['mailbag_errors'];
                    $nextid = $dbconn->GenId($xartable['mailbag_errors']);
                    $sql = "INSERT INTO $table (xar_msgid, xar_subject, xar_from, xar_from_uid, xar_to, xar_to_uid, xar_msg_time, xar_msg_text, xar_header, xar_errorcode, xar_error)
                                        VALUES ($nextid, '" . xarVarPrepForStore($subject) . "', '".xarVarPrepForStore($emailfrom)."', '$from_userid', '".xarVarPrepForStore($to)."', '$to_userid', '$time', '".xarVarPrepForStore($msg['Text'])."', '".xarVarPrepForStore($msg['Header'])."', $mailbagerrorcode, '".xarVarPrepForStore($mailerror)."')";
                    $res = $dbconn->Execute($sql);
                  */  
                }
                
                
                
                
            }
            /**
                Otherwise lets figure out the error and log it
                And/Or send an error message to mailer
            */
            else
            {
                echo $mailerror;
            }
            
            /**
                Finally delete the message
            */
            //$pop->deleteMsg($key);
            
        }// End of Loop
        
        
        $pop->disconnect();
    }
    else
    {
        echo 'Couldn\'t Connect to POP3 Server!';
    }

    // Log this run and the number of messages processed
    xarModSetVar('mailbag', 'lastrunlog', date("Y-m-d H:i").": ".$msgcount."+".$errcount);

    if ($mailbagdebug) echo "<b>MailBag is done</b><br>";
    
    return;
}
/*
define('_MAILBAGMAXRECIP', 'Max recipients per message');
define('_MAILBAGMAXSIZE', 'Max size of incoming message (bytes)');
define('_MAILBAGSENDERS', 'Senders (e-mail address)');
define('_MAILBAGRECIPIENTS', 'Recipients (without domain)');
define('_MAILBAGNUMBERMSGS', 'Number of messages on server to be processed');
define('_MAILBAGNUMBERERRLOGMSGS', 'Number of error log items to be processed');
define('_MAILBAGNOHTMLORTEXT', 'Message contains no text');
define('_MAILBAGNOTEXTHTMLNOTALLOWED', 'Message contains no plain text, and HTML is not allowed');
define('_MAILBAGROUTEDTO', 'Routed to');
define('_MAILBAGUNKNOWNRECIPIENT', 'Unknown recipient');
define('_MAILBAGMAILERROR', 'Mail error');
define('_MAILBAGCANTDELETEMSG', 'Can not delete message on server');
define('_MAILBAGMAILHEADER', 'This is an automatic error response to a message sent to our site. It could not be processed for the following reason:');
define('_MAILBAGMAILFOOTER', '--- MailBag, PostNuke E-mail Processor');
define('_MAILBAGMSGID', 'id');
define('_MAILBAGERROR', 'Error');
define('_MAILBAGERRORCODE', 'Err');
define('_MAILBAGNOSUCHITEM', 'No such error log item');
define('_MAILBAGHEADER', 'Header');
define('_MAILBAGADMINMODIFY', 'Edit error log item');
define('_MAILBAGITEMUPDATE', 'Update item');
define('_MAILBAGITEMUPDATED', 'Error log item has been updated');
define('_MAILBAGFROMERROTLOG', 'From error log (errorcode = 99)');
define('_MAILBAGCONFIGUPDATED', 'MailBag Configuration has been updated');
define('_MAILBAGMAILLISTSUPDATED', 'Mail Lists configuration has been updated');
define('_MAILBAGBLACKLISTSUPDATED', 'Black Lists configuration has been updated');
define('_MAILBAGUNKNOWNTOLOG', 'Messages to unknown recipients to error log');
define('_MAILBAGDELETEITEM', 'Delete error log item');
define('_MAILBAGITEMDELETED', 'Error log item has been deleted');
define('_MAILBAGCONFIRMDELETE', 'Confirm error log item delete');
define('_MAILBAGCANCELDELETE', 'Cancel error log item delete');
define('_MAILBAGFROMERRORLOG', 'Input from error log');
define('_MAILBAGLASTRUN', 'Last MailBag run');
define('_MAILBAGNOTIFYUSER', 'Notify user when new message arrives');
define('_MAILBAGNOTIFHEADER', 'A new message for you has arrived in your private messages inbox. Please go to the URL below to read it.');
define('_MAILBAGSENDEREMAIL', 'Include unregistered sender address in body');*/
?>
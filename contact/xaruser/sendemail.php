<?php

function contact_user_sendemail($args)
{

    // Get parameters from whatever input we need
    list($name,
         $info,
         $phone,
         $uid,
         $fname,
         $femail,
         $message,
         $subject,
         $sender_wantcopy,
         $sender_wants_contact) = xarVarCleanFromInput('name',
                                          'info',
                                          'phone',
                                          'uid',
                                          'fname',
                                          'femail',
                                          'message',
                                          'subject',
                                          'sender_wantcopy',
                                          'sender_wants_contact');


    // Security check
       if (!xarSecurityCheck('ContactOverview')) return;


    // Check arguments
    if (empty($subject)) {
        $msg = xarML('No Subject Provided for Email');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    if (empty($message)) {
        $msg = xarML('No Message Provided for Email');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    list($message) = xarModCallHooks('item',
                                     'transform',
                                     $uid,
                                     array($message));

    if(strlen($femail) > 0) {
        if(!preg_match("'^[a-z0-9_.=-]+@(?:[a-z0-9-]+\.)+([a-z]{2,3})\$'i", $femail)){
            $msg = xarML('Error in your Email Address');
            xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
            return;
        }
    }

    $sendmessage =  "Contact:\n";
    $sendmessage .= "----------------------------------------------------------------------------------\n";
    $sendmessage .= "Name: $name\n";
    $sendmessage .= "Phone: $phone\n";
    $sendmessage .= "Email: $femail\n";
    $sendmessage .= "----------------------------------------------------------------------------------\n";
    $sendmessage .= "$message\n";
    $sendmessage .= "\n";
    if ($sender_wants_contact == 1) {
    $sendmessage .= "Please contact me!\n";
    }else{
    $sendmessage .= "Do Not call!\n";
    }
    $sendmessage .= "----------------------------------------------------------------------------------\n";
    $sendmessage .= "Date and time: ".date("Y-m-d")." ".date("H:i")."\n";
    $sendmessage .= "\n";
    if (!xarModAPIFunc('mail',
                       'admin',
                       'sendmail',
                       array('info'     => $info,
                             'name'     => $name,
                             'subject'  => $subject,
                             'message'  => $sendmessage,
                             'from'     => $femail,
                             'fromname' => $fname))) return;


    xarSessionSetVar('contact_statusmsg', xarML('Message Sent',
                    'contact'));

    if ($sender_wantcopy = "on"){
    $info = $femail;
    if (!xarModAPIFunc('mail',
                       'admin',
                       'sendmail',
                       array('info'     => $info,
                             'name'     => $name,
                             'subject'  => $subject,
                             'message'  => $sendmessage,
                             'from'     => $femail,
                             'fromname' => $fname))) return;


    xarSessionSetVar('contact_statusmsg', xarML('Message Sent',
                    'contact'));
    }
    // lets update status and display updated configuration

    xarResponseRedirect(xarModURL('contact', 'user', 'mailsent',array('fname' => $fname, 'sender_wantcopy' => $sender_wantcopy)));

    // Return
    return true;
}

?>
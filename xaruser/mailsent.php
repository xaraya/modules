<?php

function contact_user_mailsent($args)

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

      if ($sender_wantcopy = "on"){
      $copy = 1;
      }
    $data['copy'] = $copy;
    $data['fname'] = $fname;
    $data['thankyou'] = xarVarPrepForDisplay(xarML('Thank You for your email:'));
    $data['willreply'] = xarVarPrepForDisplay(xarML('Your should receive a reply within 24 to 48 hours.'));
    $data['copysent'] = xarVarPrepForDisplay(xarML('A copy of your email has been sent to your inbox.'));
    $data['enjoy'] = xarVarPrepForDisplay(xarML('Please enjoy the rest of your stay.'));

 return $data;
 }
?>
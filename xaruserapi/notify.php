<?php
/**
 * Notification email for legis new, modified and deleted documents
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author jojodee
 */
/**
 * Notification by email
 *
 * @author jojodee
 * @param  $ notifytype 1 = new document, 2 = validated, 3 = invalidated,
  *                     4 = passed, 5 = notpassed, 6 = notvetoed 7 = vetoed 8 = deleted
 */
function legis_userapi_notify($args)
{
    extract($args);

    if (!isset($notifytype) || !isset($cdid)) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    // Get the document concerned
    $docdata= xarModAPIFunc('legis','user','get',array('cdid' => $cdid));

    // Get the hall
    $hall = $docdata['dochall'];
     //Get the category halls
    $halldata=xarModAPIFunc('legis','user','getsethall');
    $dochallname=$halldata['defaulthalldata']['name'];
    $notifymessage='';
    switch ($notifytype) {
    case 0:
        $notifymessage= xarML('has been modified');
        break;
    case 1:
        $notifymessage= xarML('has been added and requires reviewing');
        break;
    case 2:
        $notifymessage= xarML('has been validated and ready for voting');
       break;
    case 3:
       break;
        $notifymessage= xarML('has been marked as invalid and will be added to documents pending deletion');
    case 4:
        $notifymessage= xarML('has been voted on and passed. It is now ready for editing and final review');
      break;
    case 5:
        $notifymessage= xarML('has been voted on and but not passed');
      break;
    case 6:
        $notifymessage= xarML('has gone through final review and not vetoed');
      break;
    case 7:
         $notifymessage= xarML('has passed through final review and has been vetoed');
      break;
    case 8:
        $notifymessage= xarML('has been permanently deleted');
      break;

    }
    //Let's see if there is a subscribe list for this
    $subscribelist = xarModGetVar('legis','subscribers_'.$hall);
     if (!isset($subscribelist)) {
        //no subscribers so let's just return
        return true;
     } else {
         $subscribers = unserialize($subscribelist);
         if (is_array($subscribers) && count($subscribers) >0 ) {
             // Go through the subscribers, grab their email address and send email
             // For now just send - let's template later
             $sitename       = xarModGetVar('themes', 'SiteName');
             $hallname       = ucfirst($dochallname);
             $doclink        = xarModURL('legis', 'user', 'display', array('cdid' => $cdid),0);
             $subject        = xarML('Legislation Subscription Notice for #(1)', $sitename);
             
             $message        = xarML('A document for #(1) at #(2) #(3).', $hallname,$sitename,$notifymessage);
             $message        .= "\n\n";
             $message        .= xarML('Title: #(1)', $docdata['cdtitle']);
             $message        .= "\n\n";
             $message        .= xarML('URL Link: #(1)', $doclink);
             $htmlmessage    = xarML('A document for #(1) at #(2) #(3).', $sitename);
             $htmlmessage    .= '<p><a href="'. $doclink .'">'. $docdata['cdtitle'] .'</a>';

             foreach($subscribers as $subscriber){
                // Send the email to them
                // Gots ta gets the topic info
                $user = xarModAPIFunc('roles','user','get',
                                array('uid' => $subscriber));

                if (!xarModAPIFunc('mail',
                           'admin',
                           'sendmail',
                           array('info'         => $user['email'],
                                 'name'         => $user['name'],
                                 'subject'      => $subject,
                                 'message'      => $message,
                                 'htmlmessage'  => $htmlmessage))) return;
            }

         } else {
          //no subscribers so let's just return
        return true;
         }
     }
    return true;
}
?>
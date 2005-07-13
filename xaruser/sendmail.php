<?php
/**
 * File: $Id: s.xarinit.php 1.11 03/01/18 11:39:31-05:00 John.Cox@mcnabb. $
 * 
 * Xaraya Courses
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage helpdesk
 * @author Helpdesk Development team
*/
/**
 * @ Author jojodee/Michel V.
 * 
 * @ Function: Send an e-mail to submitter and assigned-to
 * 
 */

function helpdesk_user_sendmail($args)
{
    // Get parameters
    extract ($args);
    if (!xarVarFetch('tid', 'int::', $tid)) return; // The ticket ID
    if (!xarVarFetch('userid', 'int::', $userid)) return;
    if (!xarVarFetch('name', 'str::', $name)) return;
    if (!xarVarFetch('whosubmit', 'int', $whosubmit)) return;
    if (!xarVarFetch('phone', 'str', $phone)) return;
    if (!xarVarFetch('email', 'email:1:', $email, '')); return;
  //  if (!xarVarFetch('subject', 'str::', $subject)) return;
    if (!xarVarFetch('domain', 'str::', $domain)) return;
    if (!xarVarFetch('source', 'int:1:', $source)) return;
    if (!xarVarFetch('priority', 'int::', $priority)) return;
    if (!xarVarFetch('status', 'str::', $status)) return;
    if (!xarVarFetch('openedby', 'int::', $openedby)) return;
    if (!xarVarFetch('assignedto', 'str::', $assignedto)) return;
    if (!xarVarFetch('closedby', 'str::', $closedby, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('issue', 'str::', $issue, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('notes', 'str::', $notes, '', XARVAR_NOT_REQUIRED)) return;
    
//    if (!empty($email)) {
//    $recipients = $email;
//    } else {
    $recipients = xarUserGetVar('email');
//    }
    
    // Does not generate errors
//    if(isset($recipients)) {
        $fromname = "Webmaster"; // Get the webmaster name
        $femail =  "webmaster@intrasense.nl";
       //$mailsubject = xarVarPrepForDisplay(xarML('New ticket submitted'));
        $viewticket = xarModUrl('helpdesk', 'user', 'display', array('tid' => $tid));
        $viewtickets = xarModUrl('helpdesk', 'user', 'view', array('selection' => 'MYALL'));

    // startnew
    $textmessage= xarTplModule('helpdesk',
                                   'user',
                                   'sendmailnew-user',
                                array('userid'      => $userid,
                                      'name'        => $name,
                                      'whosubmit'   => $whosubmit,
                                      'phone'       => $phone,
                                      'email'       => $email,
                                      'subject'     => $subject,
                                      'domain'      => $domain,
                                      'source'      => $source,
                                      'priority'    => $priority,
                                      'status'      => $status,
                                      'openedby'    => $openedby,
                                      'assignedto'  => $assignedto,
                                      'closedby'    => $closedby,
                                      'issue'       => $issue,
                                      'notes'       => $notes,
                                      'viewticket'  => $viewticket,
                                      'viewtickets' => $viewtickets),
                                    'text');

     $htmlmessage= xarTplModule('helpdesk',
                                   'user',
                                   'sendmailnew-user',
                                array('userid'      => $userid,
                                      'name'        => $name,
                                      'whosubmit'   => $whosubmit,
                                      'phone'       => $phone,
                                      'email'       => $email,
                                      'subject'     => $subject,
                                      'domain'      => $domain,
                                      'source'      => $source,
                                      'priority'    => $priority,
                                      'status'      => $status,
                                      'openedby'    => $openedby,
                                      'assignedto'  => $assignedto,
                                      'closedby'    => $closedby,
                                      'issue'       => $issue,
                                      'notes'       => $notes,
                                      'viewticket'  => $viewticket,
                                      'viewtickets' => $viewtickets),
                                    'html');

        //let's send the email now
        if($usermail = xarModAPIFunc('mail',
                           'admin',
                           'sendmail',
                           array('info'         => 'webmaster@sense.nl',//$recipients,
                                 'name'         => $name,
                                 'subject'      => $mailsubject,
                                 'htmlmessage'  => $htmlmessage,
                                 'message'      => $textmessage
                                 //,
                                // 'from'         => $femail,
                                // 'fromname'     => $fromname
                                ))) {
        xarSessionSetVar('helpdesk_statusmsg', xarML('User mail sent','helpdesk'));
        } else {
            $msg = xarML('The message was not sent');
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'FUNCTION_FAILED', new SystemException($msg));
            return false;
        }
//    }

/**
 * 
 * Send an e-mail to the user with course details
 * TODO Move these to seperate functions and make them adjustable
 * @author Michel V.

    $studentmail = xarUserGetVar('email');
    if(isset($studentmail)) {
        $uid = xarUserGetVar('uid');
        $studentname = xarUserGetVar('name');
        $coordinators = $planitem['coordinators'];
        $fromname = xarUserGetVar('name', $coordinators);
        $fromemail = xarUserGetVar('email', $coordinators); 
        $viewcourse = xarModUrl('courses', 'user', 'displayplanned', array('planningid' => $planningid));
        $viewaccount = xarModUrl('roles', 'user', 'account', array('moduleload' => 'courses'));
        // Check the coordinator name/e-mail; otherwise default to webmaster
            if (!isset($planitem['coordinators']) || !is_numeric($planitem['coordinators'])) { //Or use contact address?
                $fromname = xarML('Webmaster');
                $fromemail = xarUserGetVar('mail', 'adminmail');
            }

        $subject = $studentname.', '.xarVarPrepForDisplay(xarML('you have been enrolled in:')).' '.$name;
        $messagebody = xarVarPrepForDisplay(xarML('Please go to your course page to see the full list of your courses'));
        // send email to user with details and link

    // startnew
    $textmessage= xarTplModule('courses',
                                   'user',
                                   'sendconfirmstudent',
                                    array('studentname'   => $studentname,
                                          'femail'  => $femail,
                                          'name' => $name,
                                          'username' => $username,
                                          'viewcourse' => $viewcourse,
                                          'viewaccount'   => $viewaccount,
                                          'regdate' => $regdate,
                                          'recipients' => $recipients,
                                          'course' => $course,
                                          'planitem' => $planitem),
                                    'text');

     $htmlmessage= xarTplModule('courses',
                                   'user',
                                   'sendconfirmstudent',
                                    array('studentname'   => $studentname,
                                          'femail'  => $femail,
                                          'name' => $name,
                                          'username' => $username,
                                          'viewcourse' => $viewcourse,
                                          'viewaccount'   => $viewaccount,
                                          'regdate' => $regdate,
                                          'recipients' => $recipients,
                                          'course' => $course,
                                          'planitem' => $planitem),
                                    'html');

    //let's send the email now
    if (xarModAPIFunc('mail',
                       'admin',
                       'sendmail',
                       array('info'         => $recipients,
                             'name'         => $coordinators,
                             'subject'      => $subject,
                             'htmlmessage'  => $htmlmessage,
                             'message'      => $textmessage,
                             'from'         => $femail,
                             'fromname'     => $username)));
        
    xarSessionSetVar('courses_statusmsg', xarML('Student notification Sent','courses'));
    } else {
        $msg = xarML('The message was not sent to the student');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'FUNCTION_FAILED', new SystemException($msg));
        return;
    }
 */
    // Return
    return true;
}
?>

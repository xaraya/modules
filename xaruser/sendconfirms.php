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
 * @subpackage Courses
 * @author Courses Development team
*/
/**
 * @ Author jojodee/Michel V.
 * 
 * @ Function: Send an e-mail to the coordinator to notify about the enrollment
 * @ parameters Takes parameters passed by user_sendtofriend to generate info used by email mod
 * 
 * 
 */

function courses_user_sendconfirms($args)
{
    // Get parameters
    extract ($args);
    if (!xarVarFetch('studstatus', 'int:1:', $studstatus)) return; // Doesn't generate an int
    if (!xarVarFetch('userid', 'str::', $userid)) return;
    if (!xarVarFetch('planningid', 'int:1:', $planningid)) return;
   // if (!xarVarFetch('enrollid', 'int:1:', $enrollid)) return;
    // Get planned course
    $planitem = xarModAPIFunc('courses',
                          'user',
                          'getplanned',
                          array('planningid' => $planningid));
    if (!isset($planitem) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    // Get course
    $course = xarModAPIFunc('courses',
                          'user',
                          'get',
                          array('courseid' => $planitem['courseid']));

    $name = $course['name'];
    // Check to see if coordinator exists
    $coordinators = $course['contactuid'];
    // Without coordinator, send mail to AlwaysNotify
    if (!isset($course['contactuid']) || !is_numeric($course['contactuid'])) {
        $recipients = xarModGetVar('courses', 'AlwaysNotify'); 
    } elseif (isset($course['contactuid']) && is_numeric($course['contactuid'])) {
        $recipients = xarUserGetVar('email', $coordinators).','.xarModGetVar('courses', 'AlwaysNotify');    
    } else {
        $msg = xarML('Wrong arguments to courses_enroll', join(', ', $invalid), 'user', 'enroll', 'Courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    // Does not generate errors
    if(isset($recipients)) {
        $uid = xarUserGetVar('uid');
        $username = xarUserGetVar('name');
        $fromname = "Webmaster"; // Get the webmaster name
        $femail =  xarUserGetVar('email');
        $subject = $username.' '.xarVarPrepForDisplay(xarML('enrolled in your course:')).' '.$name;
        $viewcourse = xarModUrl('courses', 'user', 'displayplanned', array('planningid' => $planningid));
        $viewaccount = xarModUrl('roles', 'user', 'account', array('moduleload' => 'courses'));

    // startnew
    $textmessage= xarTplModule('courses',
                                   'user',
                                   'sendconfirmcoordinator',
                                    array('username'   => $username,
                                          'femail'  => $femail,
                                          'name' => $name,
                                          'viewcourse' => $viewcourse,
                                          'startdate'=> $planitem['startdate'],
                                          'viewaccount'   => $viewaccount,
                                          'regdate' => $regdate,
                                          'recipients' => $recipients,
                                          'course' => $course,
                                          'planitem' => $planitem),
                                    'text');

     $htmlmessage= xarTplModule('courses',
                                   'user',
                                   'sendconfirmcoordinator',
                                    array('username'   => $username,
                                          'femail'  => $femail,
                                          'name' => $name,
                                          'viewcourse' => $viewcourse,
                                          'startdate'=> $planitem['startdate'],
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
                             'fromname'     => $username))) {
        
    xarSessionSetVar('courses_statusmsg', xarML('Coordinator Notification Sent','courses'));
    } else {
        $msg = xarML('The message was not sent');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'FUNCTION_FAILED', new SystemException($msg));
        return;
    }
    
/**
 * 
 * Send an e-mail to the user with course details
 * TODO Move these to seperate functions and make them adjustable
 * @author Michel V.
 */
    $studentmail = xarUserGetVar('email');
    if(isset($studentmail)) {
        $uid = xarUserGetVar('uid');
        $studentname = xarUserGetVar('name');
        $coordinators = $course['contactuid'];
        $fromname = xarUserGetVar('name', $coordinators);
        $fromemail = xarUserGetVar('email', $coordinators); 
        $viewcourse = xarModUrl('courses', 'user', 'displayplanned', array('planningid' => $planningid));
        $viewaccount = xarModUrl('roles', 'user', 'account', array('moduleload' => 'courses'));
        // Check the coordinator name/e-mail; otherwise default to webmaster
            if (!isset($course['contactuid']) || !is_numeric($course['contactuid'])) { //Or use contact address?
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

    // Return
    return true;
}
?>

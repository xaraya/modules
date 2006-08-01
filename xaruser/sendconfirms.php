<?php
/**
 * Send confirmation emails
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses Development team
*/
/**
 * Send emails to confirm enrolling etc.
 *
 * Send an e-mail to the coordinator to notify about the enrollment
 * @param Takes parameters passed by user_sendtofriend to generate info used by email mod
 * @author jojodee/MichelV.
 * @param int studstatus The ID of the status of the student
 * @param int userid The userid of the student
 * @param int planningid The ID of the planned course
 * @param int enrollid  The created ID for this enrollment the function sends emails for.
 * @return bool true on success
 */
function courses_user_sendconfirms($args)
{
    // Get parameters
    extract ($args);
    if (!xarVarFetch('studstatus', 'int:1:', $studstatus)) return;
    if (!xarVarFetch('userid',     'str::',  $userid))     return;
    if (!xarVarFetch('planningid', 'id',     $planningid)) return;
    if (!xarVarFetch('enrollid',   'id',     $enrollid))   return;
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
    if (!isset($course) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    // Check for Waiting List status, as that changes the texts

    $waitingid = xarModGetVar('courses','WaitingListID');
    if ($waitingid == $studstatus) {
        $waitinglist = true;
    } else {
        $waitinglist = false;
    }

    $coursename = $course['name'];

    // Check to see if coordinator exists
    if (!empty ($course['contactuid'])) {
        $coordinator = $course['contactuid'];
        $coordname = xarUserGetVar('name', $coordinator);
    } else {
        $coordinator = '';
    }

    // Without coordinator, send mail to AlwaysNotify
    if (empty($coordinator) || !is_numeric($coordinator)) {
        $recipients = array(xarModGetVar('courses', 'AlwaysNotify'));
    } elseif (is_numeric($coordinator)) {
        $recipients = array(xarUserGetVar('email', $coordinator), xarModGetVar('courses', 'AlwaysNotify'));
    } else {
        // TODO: Evaluate this, do we want an error to be thrown?
        $msg = xarML('Wrong arguments to courses_enroll', join(', ', $invalid), 'user', 'enroll', 'Courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    // Does not generate errors
    if(isset($recipients)) {
        //$uid = xarUserGetVar('uid');
        $username = xarUserGetVar('name', $userid);
        $fromname = xarModGetVar('mail', 'adminname');
        $femail =  xarUserGetVar('email', $userid);
        if ($waitinglist) {
            $subject = $username.' '.xarVarPrepForDisplay(xarML('is on the waitinglist for:')).' '.$coursename;
        } else {
            $subject = $username.' '.xarVarPrepForDisplay(xarML('enrolled in your course:')).' '.$coursename;
        }
        $viewcourse = xarModUrl('courses', 'user', 'displayplanned', array('planningid' => $planningid));
        $viewaccount = xarModUrl('roles', 'user', 'account', array('moduleload' => 'courses'));

        // start new message
        $textmessage= xarTplModule('courses',
                                       'user',
                                       'sendconfirmcoordinator',
                                        array('username'   => $username,
                                              'coordname'  => $coordname,
                                              'femail'     => $femail,
                                              'name'       => $coursename,
                                              'viewcourse' => $viewcourse,
                                              'startdate'  => $planitem['startdate'],
                                              'viewaccount'=> $viewaccount,
                                              'regdate'    => $regdate,
                                              'recipients' => $recipients,
                                              'course'     => $course,
                                              'waitinglist'=> $waitinglist,
                                              'planitem'   => $planitem),
                                         'text');

        $htmlmessage= xarTplModule('courses',
                                      'user',
                                      'sendconfirmcoordinator',
                                       array('username'    => $username,
                                             'coordname'  => $coordname,
                                             'femail'      => $femail,
                                             'name'        => $coursename,
                                             'viewcourse'  => $viewcourse,
                                             'startdate'   => $planitem['startdate'],
                                             'viewaccount' => $viewaccount,
                                             'regdate'     => $regdate,
                                             'recipients'  => $recipients,
                                             'course'      => $course,
                                             'waitinglist'=> $waitinglist,
                                             'planitem'    => $planitem),
                                        'html');
        //let's send the email now
        if (xarModAPIFunc('mail',
                          'admin',
                          'sendmail',
                           array('recipients'   => $recipients,
                                 'name'         => $coordname,
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
    }
/**
 *
 * Send an e-mail to the user with course details
 * @TODO Move these to seperate functions and make them adjustable
 * @author MichelV.
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
        if ($waitinglist) {
            $subject = $studentname.', '.xarVarPrepForDisplay(xarML('you are on the waitinglist for:')).' '.$coursename;
        } else {
            $subject = $studentname.', '.xarVarPrepForDisplay(xarML('you have been enrolled in:')).' '.$coursename;
        }
        // send email to user with details and link

        // start new message to the student
        // TODO: Why pass the complete planitem, security?
        $textmessage= xarTplModule('courses',
                                       'user',
                                       'sendconfirmstudent',
                                        array('studentname' => $studentname,
                                              'femail'      => $fromemail,
                                              'name'        => $coursename,
                                              'username'    => $username,
                                              'viewcourse'  => $viewcourse,
                                              'viewaccount' => $viewaccount,
                                              'regdate'     => $regdate,
                                              'recipients'  => $recipients,
                                              'course'      => $course,
                                              'waitinglist' => $waitinglist,
                                              'planitem'    => $planitem),
                                        'text');

         $htmlmessage= xarTplModule('courses',
                                       'user',
                                       'sendconfirmstudent',
                                        array('studentname' => $studentname,
                                              'femail'      => $femail,
                                              'name'        => $coursename,
                                              'username'    => $username,
                                              'viewcourse'  => $viewcourse,
                                              'viewaccount' => $viewaccount,
                                              'regdate'     => $regdate,
                                              'recipients'  => $recipients,
                                              'course'      => $course,
                                              'waitinglist' => $waitinglist,
                                              'planitem'    => $planitem),
                                        'html');

        //let's send the email now
        if (xarModAPIFunc('mail',
                           'admin',
                           'sendmail',
                           array('info'         => $studentmail,
                                 'name'         => $studentname,
                                 'subject'      => $subject,
                                 'htmlmessage'  => $htmlmessage,
                                 'message'      => $textmessage,
                                 'from'         => $fromemail,
                                 'fromname'     => $fromname))) {

             xarSessionSetVar('courses_statusmsg', xarML('Student notification Sent','courses'));
        } else {
            $msg = xarML('The message was not sent to the student');
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'FUNCTION_FAILED', new SystemException($msg));
            return;
        }
    }
    // Return
    return true;
}
?>

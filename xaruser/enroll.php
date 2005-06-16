<?php
 /**
 * File: $Id:
 *
 * Enroll student in course
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2004 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage courses
 * @author XarayaGeek/Michel V.
 */

/**
 * Enroll a user into a course and update database
 *
 * @param  $args an array of arguments (if called by other modules)
 * @param  $args ['objectid'] a generic object id (if called by other modules)
 * @param  $args ['planningid'] the planned course ID that the user will enroll to
 */
function courses_user_enroll($args)
{

 if (!xarSecurityCheck('ReadCourses', 0)) {
        return $data['error'] = xarML('You must be a registered user to enroll in courses.');
    }

 extract($args);

  if (!xarVarFetch('planningid', 'int::', $planningid, NULL, XARVAR_DONT_SET)) return;
  if (!xarVarFetch('objectid', 'str:1:', $objectid, '', XARVAR_NOT_REQUIRED)) return;
  if (!xarVarFetch('message', 'str:1:', $message, '', XARVAR_NOT_REQUIRED)) return;

    $courses['transform'] = array('name');
    $item = xarModCallHooks('item',
        'transform',
        $planningid,
        $courses);
    //check for override by objectid
    if (!empty($objectid)) {
        $planningid = $objectid;
    }
    // Get the username so we can pass it to the enrollment function
    $uid = xarUserGetVar('uid');
    //Check to see if this user is already enrolled in this course
    $enrolled = xarModAPIFunc('courses',
                          'user',
                          'check_enrolled',
                          array('uid' => $uid,
                                'planningid' => $planningid));

    //if (!isset($courses) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (count($enrolled)!=0) {
    $msg = xarML('You are already enrolled in this course');
        xarErrorSet(XAR_USER_EXCEPTION, 'ALREADY_ENROLLED',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }

        $item = xarModAPIFunc('courses',
        'user',
        'getplanned',
        array('planningid' => $planningid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    
/* //Check this mailing function later on.
     
     //Rewrite the name to get
     $name = $item['name'];
//       echo "<br /><pre>items => "; print_r($item); echo "</pre>";
     $message = xarVarPrepForDisplay(xarML('A new user has enrolled in '. $name ));
     $uid = xarUserGetVar('uid');
     $uname = xarUserGetVar('uname');
     $info = xarModGetVar('mail', 'adminmail', 1);
     $fname = "Webmaster";
     $femail =  xarUserGetVar('email');
     $subject = "New Enrollment";

    // send email to admin notifying them that someone has enrolled
    $sendmessage =  "Courses:\n";
    $sendmessage .= "----------------------------------------------------------------------------------\n";
    $sendmessage .= "Name: $uname\n";
    $sendmessage .= "Userid: $uid\n";
    $sendmessage .= "Email: $femail\n";
    $sendmessage .= "----------------------------------------------------------------------------------\n";
    $sendmessage .= "$message\n";
    $sendmessage .= "\n";
    $sendmessage .= "----------------------------------------------------------------------------------\n";
    $sendmessage .= "Date and time: ".date("Y-m-d")." ".date("H:i")."\n";
    $sendmessage .= "\n";
    if (!xarModAPIFunc('mail',
                       'admin',
                       'sendmail',
                       array('info'     => $info,
                             'name'     => $uname,
                             'subject'  => $subject,
                             'message'  => $sendmessage,
                             'from'     => $femail,
                             'fromname' => $fname))) return;

    
    xarSessionSetVar('courses_statusmsg', xarML('Message Sent',
                    'courses'));
*/

    // If user is not enrolled already go ahead and create the enrollment
    // Get status of student
    $studstatus = 1;
    $enrollid = xarModAPIFunc('courses',
                          'user',
                          'create_enroll',
                          array('uid'        => $uid,
                                'planningid' => $planningid,
                                'studstatus' => $studstatus));
    // The return value of the function is checked here, and if the function
    // suceeded then an appropriate message is posted.  Note that if the
    // function did not succeed then the API function should have already
    // posted a failure message so no action is required
    if (!isset($enrollid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('courses', 'user', 'displayplanned', array('planningid' => $planningid, 'courseid' => $item['courseid'])));
    // Return
    return true;

}
?>

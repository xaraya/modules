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
 * @author XarayaGeek
 */

/**
 * Enroll a user into a course and update database
 *
 * @param  $args an array of arguments (if called by other modules)
 * @param  $args ['objectid'] a generic object id (if called by other modules)
 * @param  $args ['courseid'] the item id used for this course module
 */
function courses_user_enroll($args)
{

 if (!xarSecurityCheck('EditCourses', 0)) {
        return $data['error'] = xarML('You must be a registerd user to enroll in courses.');
    }

 extract($args);
 // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarFetch(), xarVarCleanFromInput()
    // is a degraded function.  xarVarFetch allows the checking of the input
    // variables as well as setting default values if needed.  Getting vars
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya

  if (!xarVarFetch('courseid', 'isset:', $courseid, NULL, XARVAR_DONT_SET)) return;
  if (!xarVarFetch('message', 'str:1:', $message, '', XARVAR_NOT_REQUIRED)) return;

    // At this stage we check to see if we have been passed $objectid, the
    // generic item identifier.  This could have been passed in by a hook or
    // through some other function calling this as part of a larger module, but
    // if it exists it overrides $exid
    $courses['transform'] = array('name');
    $item = xarModCallHooks('item',
        'transform',
        $courseid,
        $courses);

    // Note that this module couuld just use $objectid everywhere to avoid all
    // of this munging of variables, but then the resultant code is less
    // descriptive, especially where multiple objects are being used.  The
    // decision of which of these ways to go is up to the module developer
    if (!empty($objectid)) {
        $courseid = $objectid;
    }
    // Get the username so we can pass it to the enrollment function
    $uid = xarUserGetVar('uid');
	//Check to see if this user is already enrolled in this course
    $courses = xarModAPIFunc('courses',
                          'user',
                          'check_enrolled',
                          array('uid' => $uid,
                                'courseid' => $courseid));

    if (!isset($courses) && xarExceptionMajor() != XAR_NO_EXCEPTION) return; // throw back

    if (!isset($courses[$courseid])) {
        $item = xarModAPIFunc('courses',
        'user',
        'get',
        array('courseid' => $courseid));
    if (!isset($item) && xarExceptionMajor() != XAR_NO_EXCEPTION) return; // throw back
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
   }

    if (isset($courses[$courseid])) {
    $msg = xarML('You are already enrolled in this course');
        xarExceptionSet(XAR_USER_EXCEPTION, 'ALLREADY_ENROLLED',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }

    // If user is not enroilled already go ahead and create the enrollment
    $enrollid = xarModAPIFunc('courses',
                          'user',
                          'create_enroll',
                          array('uid' => $uid,
                                'courseid' => $courseid));
    // The return value of the function is checked here, and if the function
    // suceeded then an appropriate message is posted.  Note that if the
    // function did not succeed then the API function should have already
    // posted a failure message so no action is required
    if (!isset($enrollid) && xarExceptionMajor() != XAR_NO_EXCEPTION) return; // throw back
    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('courses', 'user', 'pay', array('courseid' => $courseid)));
    // Return
    return true;

}
?>

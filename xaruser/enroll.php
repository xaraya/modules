<?php
/**
 * Enroll student in course
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses Development team
 */

/**
 * Enroll a user into a course and update database
 * @Author XarayaGeek/Michel V.
 *
 * @param  $args an array of arguments (if called by other modules)
 * @param  $args ['objectid'] a generic object id (if called by other modules)
 * @param  $args ['planningid'] the planned course ID that the user will enroll to
 * @Access PUBLIC
 *
 */
function courses_user_enroll($args)
{
    // User must be logged in and have privilege
    if (!xarSecurityCheck('ReadCourses', 0) ||!xarUserIsLoggedIn()) {
        return $data['error'] = xarML('You must be logged in to enroll in this course. Please register and login');
    }

    extract($args);

    if (!xarVarFetch('planningid', 'id',     $planningid, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('objectid',   'id',     $objectid,   '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('message',    'str:1:', $message,    '', XARVAR_NOT_REQUIRED)) return;

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
    if (count($enrolled)!=0) {
        $msg = xarML('You are already enrolled in this course');
        xarErrorSet(XAR_USER_EXCEPTION, 'ALREADY_ENROLLED', // Or other exception here?
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }

    // Get planned course
    $planitem = xarModAPIFunc('courses',
                          'user',
                          'getplanned',
                          array('planningid' => $planningid));
    if (!isset($planitem) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // If user is not enrolled already go ahead and create the enrollment
    // Get status of student; for the moment standard status is 1
    // TODO: make admin configurable
    $studstatus = 1;


    $enrollid = xarModAPIFunc('courses',
                              'user',
                              'create_enroll',
                              array('uid'        => $uid,
                                    'planningid' => $planningid,
                                    'studstatus' => $studstatus));
    if (!isset($enrollid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    // TODO: make this a better one
    $regdate = time();
    // Call sendconfirm messages
    $confirm = xarModFunc('courses',
                              'user',
                              'sendconfirms',
                              array('userid'     => xarUserGetVar('uid'),
                                    'planningid' => $planningid,
                                    'studstatus' => $studstatus,
                                    'regdate'    => $regdate,
                                    'enrollid'   => $enrollid
                                    ));
    if(!$confirm) return false;
    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('courses', 'user', 'displayplanned', array('planningid' => $planningid)));
    // Return
    return true;

}
?>

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
 * @author Michel V.
 */

/**
 * Enroll a user into a course and update database
 * @author Michel V.
 *
 * @param  $args an array of arguments (if called by other modules)
 * @param  $args an array of arguments (if called by other modules)
 * @param  $args ['objectid'] a generic object id (if called by other modules)
 * @param  $args ['planningid'] the planned course ID that the user will enroll to
 */
function courses_admin_newteacher($args)
{

 if (!xarSecurityCheck('EditPlanning', 0)) {
        return $data['error'] = xarML('You must be a registered user to enroll in courses.');
    }

 extract($args);

  if (!xarVarFetch('planningid', 'int::', $planningid, NULL, XARVAR_DONT_SET)) return;
  if (!xarVarFetch('userid', 'int::', $userid, NULL, XARVAR_DONT_SET)) return;
  if (!xarVarFetch('objectid', 'str:1:', $objectid, '', XARVAR_NOT_REQUIRED)) return;
  if (!xarVarFetch('message', 'str:1:', $message, '', XARVAR_NOT_REQUIRED)) return;

    //check for override by objectid
    if (!empty($objectid)) {
        $planningid = $objectid;
    }
    // Get the username so we can pass it to the enrollment function
    //Check to see if this user is already enrolled in this course
    $check = xarModAPIFunc('courses',
                          'admin',
                          'check_teacher',
                          array('userid' => $userid,
                                'planningid' => $planningid));

    //if (!isset($courses) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    
    // Check if this teacher is already a teacher
    if (count($check)!=0) {
    $msg = xarML('You are already a teacher in this course');
        xarErrorSet(XAR_USER_EXCEPTION, 'ALREADY_TEACHER',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }

        $item = xarModAPIFunc('courses',
        'user',
        'getplanned',
        array('planningid' => $planningid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    
    // If user is not enrolled already go ahead and create the enrollment
    // Get status of student
    $type = 1;
    $tid = xarModAPIFunc('courses',
                          'admin',
                          'create_teacher',
                          array('userid'     => $userid,
                                'planningid' => $planningid,
                                'type' => $type));

    if (!isset($tid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('courses', 'admin', 'teachers', array('planningid' => $planningid)));
    // Return
    return true;

}
?>

<?php
/**
 * Enroll student in course
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses Development team
 */
/**
 * Enroll a user into a course.
 *
 * The factual enrollment is performed by an update in the database by the API function
 * @Author XarayaGeek/Michel V.
 *
 * @param array $args an array of arguments (if called by other modules)
 * @param int $args ['objectid'] a generic object id (if called by other modules)
 * @param int  $args ['planningid'] the planned course ID that the user will enroll to
 * @param confirm OPTIONAL OR
 * @param noconfirm OPTIONAL
 * @access PUBLIC
 * @return mixed true on successfull enrollment, array with data for template when information is incomplete
            or a confirmation is required.
 * @todo MichelV <1> Create admin configurable standard student status
 */
function courses_user_enroll($args)
{
    extract($args);

    if (!xarVarFetch('planningid', 'id',     $planningid, NULL,  XARVAR_DONT_SET)) return;
    if (!xarVarFetch('objectid',   'id',     $objectid,   '',    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('message',    'str:1:', $message,    '',    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm',    'isset',  $confirm,    false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('noconfirm',  'isset',  $noconfirm,  false, XARVAR_NOT_REQUIRED)) return;
    //check for override by objectid
    if (!empty($objectid)) {
        $planningid = $objectid;
    }
    $data = array();
    // Get the username so we can pass it to the enrollment function
    $uid = xarUserGetVar('uid');

    // User must be logged in and have privilege
    if (!xarSecurityCheck('ReadCourses', 0) || !xarUserIsLoggedIn()) {
        $data['loginerror'] = xarML('You must be logged in to enroll in this course.');
        $regmoduleinfo = xarModGetInfo(xarModGetVar('roles', 'defaultregmodule'));
        $authmoduleinfo = xarModGetinfo(xarModGetVar('roles', 'defaultauthmodule'));
        $data['loginurl'] = xarModURL($authmoduleinfo['name'],'user','main');
        $data['regurl'] = xarModURL($regmoduleinfo['name'],'user','main');
        return $data;
    }

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

    // See if we have an external registration
    $use_extreg = $planitem['extreg'] ? true : false;
    $sendalways = xarModGetVar('courses', 'SendConfirmsForExtreg') ? true : false;
    // User does not want to register
    if ($noconfirm) {
        xarResponseRedirect(xarModURL('courses', 'user', 'displayplanned', array('planningid' => $planningid)));
    }
    if (!$confirm) {
        // No confirmation yet, present form
        // How many student are enrolled already?
        $s_count = xarModApiFunc('courses','user','countparticipants', array('planningid',$planningid));
        // Set the correct status
        if (($planitem['maxparticipants'] > 0) && ($s_count >= $planitem['maxparticipants'])) {
            $data['WaitingList'] = true;
        } else {
            $data['WaitingList'] = false;
        }
        $data['planitem'] = $planitem;
        $data['confirm'] = $confirm;
        $data['planningid'] = $planningid;
        $data['use_extreg'] = $use_extreg;
        $data['authid'] = xarSecGenAuthKey();
        $data['coursename'] = xarModApiFunc('courses','user','getcoursename',array('planningid' => $planningid));
        // Show confirmation form
        return $data;

    } elseif ($confirm) {
        // If user is not enrolled already go ahead and create the enrollment

        // How many student are enrolled already?
        $s_count = xarModApiFunc('courses','user','countparticipants', array('planningid',$planningid));
        // Set the correct status
        if (($planitem['maxparticipants'] > 0) && ($s_count >= $planitem['maxparticipants'])) {
            $studstatus = xarModGetVar('courses','WaitingListID');
        } else {
            $studstatus = xarModGetVar('courses','StandardEnrollID');
        }
        // Create the actual enrollment
        $enrollid = xarModAPIFunc('courses',
                                  'user',
                                  'create_enroll',
                                  array('uid'        => $uid,
                                        'planningid' => $planningid,
                                        'studstatus' => $studstatus));
        if (!isset($enrollid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

        if (!$use_extreg || ($use_extreg && $sendalways)) {
            // TODO: make this a better one
            $regdate = time();
            // Call sendconfirm messages
            $sendconfirm = xarModFunc('courses',
                                      'user',
                                      'sendconfirms',
                                      array('userid'     => xarUserGetVar('uid'),
                                            'planningid' => $planningid,
                                            'studstatus' => $studstatus, // This will also pass the WaitingList status
                                            'regdate'    => $regdate,
                                            'enrollid'   => $enrollid
                                            ));
            if(!$sendconfirm) return false;
            xarSessionSetVar('statusmsg', xarML('You have been enrolled'));
        }
        // redirect to the external registration
        if ($use_extreg && !empty($planitem['regurl'])) {
            xarResponseRedirect($planitem['regurl']);
            return true;
        }
    }

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('courses', 'user', 'displayplanned', array('planningid' => $planningid)));
    // Return
    return true;

}
?>

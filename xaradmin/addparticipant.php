<?php
/**
 * Enroll student in course by his userid
 *
 * @package modules
 * @copyright (C) 2006 The Digital Development Foundation
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
 * @author MichelV <michelv@xarayahosting.nl>
 *
 * @param array $args an array of arguments (if called by other modules)
 * @param int $args ['objectid'] a generic object id (if called by other modules)
 * @param int  $args ['planningid'] the planned course ID that the user will enroll to
 * @param confirm OPTIONAL OR
 * @param noconfirm OPTIONAL
 * @access PUBLIC
 * @since 6 Oct 2006
 * @return mixed true on successfull enrollment, array with data for template when information is incomplete
            or a confirmation is required.
 * @todo MichelV <1> Create admin configurable standard student status
 */
function courses_admin_addparticipant($args)
{
    extract($args);

    if (!xarVarFetch('planningid',          'id',       $planningid,       NULL,    XARVAR_DONT_SET)) return;
    if (!xarVarFetch('objectid',            'id',       $objectid,         '',      XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('userid',              'int:1:',   $userid,           $userid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sendnotification',    'checkbox', $sendnotification, false,   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl',           'str:1:',   $returnurl,        '',      XARVAR_NOT_REQUIRED)) return;
    //check for override by objectid
    if (!empty($objectid)) {
        $planningid = $objectid;
    }
    if ((empty($planningid)) || !is_numeric($planningid)) {
        return false;
    }
    if (!xarSecurityCheck('EditCourses', 0, 'course',"All:$planningid:All")) {
        return;
    }
    $data = array();
    // Get the username so we can pass it to the enrollment function
    if (empty($userid) || !is_numeric($userid)) {
        return false;
    } else {
        $uid = $userid;

        //Check to see if this user is already enrolled in this course
        $enrolled = xarModAPIFunc('courses',
                                  'user',
                                  'check_enrolled',
                                  array('uid' => $uid,
                                        'planningid' => $planningid));
        if (count($enrolled)!=0) {
            $msg = xarML('This user is already enrolled in this course');
            xarErrorSet(XAR_USER_EXCEPTION, 'ALREADY_ENROLLED', // Or other exception here?
                new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
            return;
        }
        if (!xarSecConfirmAuthKey('courses-addparticipant')) return;
        // Get planned course
        $planitem = xarModAPIFunc('courses',
                              'user',
                              'getplanned',
                              array('planningid' => $planningid));
        if (!isset($planitem) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

        // Create the enrollment for this userid
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

        if ($sendnotification) {
        // Send the emails

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
            xarSessionSetVar('statusmsg', xarML('The user has been enrolled'));
        }

        // This function generated no output, and so now it is complete we redirect
        // the user to an appropriate page for them to carry on their work
        if (!empty($returnurl)) {
            xarResponseRedirect($returnurl);
        } else {
            xarResponseRedirect(xarModURL('courses', 'user', 'displayplanned', array('planningid' => $planningid)));
        }
    }
    // Return bool
    return true;

}
?>

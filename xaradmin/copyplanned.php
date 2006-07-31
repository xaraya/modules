<?php
/**
 * Copy a planned course
 *
 * @package modules
 * @copyright (C) 2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */

/**
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('courses','admin','plancourse') to create a new planning
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param id $planningid ID of the planning to copy and create a new planning for
 * @param bool $confirm true to confirm the copy action. If false, this function will show a confirmation form
 * @since 02 May 2006
 * @return bool true on success
 */
function courses_admin_copyplanned($args)
{
    extract($args);

    // Get parameters from whatever input we need.
    if (!xarVarFetch('planningid', 'id',   $planningid)) return;
    if (!xarVarFetch('confirm',    'bool', $confirm, false, XARVAR_NOT_REQUIRED)) return;

    // Security check
    if (!xarSecurityCheck('AddCourses', 1)) return;

    // check if we have a confirm
    if (!$confirm) {
        // show a confirming template
        return array('planningid' => $planningid,
                     'coursename' => xarModApiFunc('courses','user','getcoursename',array('planningid'=> $planningid)),
                     'confirm'    => $confirm);
    }
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    $planning = xarModApiFunc('courses','user','getplanned',array('planningid'=>$planningid));
    $courseid = $planning['courseid'];
    // Security check
    if (!xarSecurityCheck('AddCourses', 1,'Course', "$courseid:All:All")) return;
    // Initialise variables
    $startdate = 0;
    $enddate = 0;
    $expected = '';
    $closedate = 0;
    // Standard hide this planning
    $hideplanning = 1;
    // Create planning and get planningid
    $newplanningid = xarModAPIFunc('courses',
                          'admin',
                          'createplanning',
                          array('courseid'      => $courseid,
                                'year'          => $planning['courseyear'],
                                'credits'       => $planning['credits'],
                                'creditsmin'    => $planning['creditsmin'],
                                'creditsmax'    => $planning['creditsmax'],
                                'startdate'     => $startdate,
                                'enddate'       => $enddate,
                                'expected'      => $expected,
                                'prerequisites' => $planning['prerequisites'],
                                'aim'           => $planning['aim'],
                                'method'        => $planning['method'],
                                'language'      => $planning['language'],
                                'longdesc'      => $planning['longdesc'],
                                'costs'         => $planning['costs'],
                                'committee'     => $planning['committee'],
                                'coordinators'  => $planning['coordinators'],
                                'lecturers'     => $planning['lecturers'],
                                'location'      => $planning['location'],
                                'material'      => $planning['material'],
                                'info'          => $planning['info'],
                                'program'       => $planning['program'],
                                'regurl'        => $planning['regurl'],
                                'extreg'        => $planning['extreg'],
                                'hideplanning'  => $hideplanning,
                                'minparticipants' => $planning['minparticipants'],
                                'maxparticipants' => $planning['maxparticipants'],
                                'closedate'     => $closedate)
                          );
    // Check for result
    if (!isset($newplanningid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarSessionSetVar('statusmsg', xarML('Successfully Copied Planning!'));
    xarResponseRedirect(xarModURL('courses', 'admin', 'modifyplanned', array('planningid' => $newplanningid)));
    // Return
    return true;
}

?>

<?php
 /**
 * Add a teacher to a course
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 */

/**
 * Combine a teacher (Xar user) with a planned course and update database
 * @author Michel V.
 *
 * @param  $args an array of arguments (if called by other modules)
 * @param  $args an array of arguments (if called by other modules)
 * @param  $args ['userid'] the uid of the role to be treated as a teacher
 * @param  $args ['planningid'] the planned course ID that the teacher will get attached to
 */
function courses_admin_newteacher($args)
{
    extract($args);

    if (!xarVarFetch('planningid',   'id',    $planningid,  NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('userid',       'int::', $userid,      NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('teachertype',  'int',   $teachertype, NULL, XARVAR_DONT_SET)) {return;}
    // Check if this teacher is already a teacher
    $check = xarModAPIFunc('courses',
                           'admin',
                           'check_teacher',
                           array('userid' => $userid,
                                 'planningid' => $planningid));

    if (count($check)!=0) {
    $msg = xarML('This teacher has already been assigned to this course');
        xarErrorSet(XAR_USER_EXCEPTION, 'ALREADY_TEACHER',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }

    $item = xarModAPIFunc('courses',
                         'user',
                         'getplanned',
                         array('planningid' => $planningid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // If user is not a teacher yet go ahead and create the teacher id
    // Create the teacher

    if (!$teachertype) {
        $teachertype = 1; // TODO set types of teachers xarModGetVar('courses','DefaultTeacherType'),
    }
    $tid = xarModAPIFunc('courses',
                         'admin',
                         'create_teacher',
                         array('userid'     => $userid,
                               'planningid' => $planningid,
                               'teachertype' => $teachertype));

    if (!isset($tid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
/*
    // Register an EDIT privilege for the newborn teacher
    // define the new instance
    $newinstance = array();
    $newinstance[] = empty($planningid) ? 'All' : $planningid;
    $newinstance[] = empty($uid) ? 'All' : $uid;
    $newinstance[] = empty($courseid) ? 'All' : $courseid;
    $extname = 'EditPlanning';
    $extrealm = 'All';
    $extmodule = 'courses';
    $extcomponent = 'Planning';
    $extlevel = '500';
    if (!empty($planningid)) {
        // create/update the privilege
        $pid = xarReturnPrivilege($extpid,$extname,$extrealm,$extmodule,$extcomponent,$newinstance,$extlevel);
        if (empty($pid)) {
            return;  // throw back
        }
    }
*/
    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarSessionSetVar('statusmsg', xarML('Teacher created successfully'));
    xarResponseRedirect(xarModURL('courses', 'admin', 'teachers', array('planningid' => $planningid)));
    // Return
    return true;

}
?>

<?php
/**
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * delete a course
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param  $ 'courseid' the id of the course to be deleted
 * @param  $ 'confirm' confirm that this item can be deleted
 */
function courses_admin_deletecourse($args)
{
    extract($args);
    if (!xarVarFetch('courseid', 'id', $courseid)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'isset', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $courseid = $objectid;
    }

    xarSessionSetVar('statusmsg','');

    // Get the course.
    $item = xarModAPIFunc('courses', 'user', 'get',
                        array('courseid' => $courseid));
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Security check
    if (!xarSecurityCheck('DeleteCourses', 1, 'Course', "$courseid:All:All")) {
        return;
    }
    // Check for confirmation.
    if (empty($confirm)) {
        // No confirmation yet - display a suitable form to obtain confirmation
        // of this action from the user
        $data = xarModAPIFunc('courses', 'admin', 'menu');
        // Specify for which item you want confirmation
        $data['courseid'] = $courseid;
        // Add some other data you'll want to display in the template
        $data['confirmtext'] = xarML('Confirm deleting this item ?');
        $data['itemid'] = xarML('Item ID');
        $data['namelabel'] = xarML('Course Name');
        $data['namevalue'] = xarVarPrepForDisplay($item['name']);
        $data['confirmbutton'] = xarML('Confirm');
        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();
        // Return the template variables defined in this function
        return $data;
    }
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    // TODO: Check for existing links with participants and planned courses. Only allow delete when none are present.
    // Get the dates this course is planned

    $countplanned = count(xarModAPIFunc('courses',
                                        'user',
                                        'getplandates',
                                        array('courseid' => $courseid)));

    if (($countplanned) > 0) {
        xarSessionSetVar('statusmsg', xarML('This course has been planned #(1) times', $countplanned));
        xarResponseRedirect(xarModURL('courses', 'admin', 'deletecourse', array('courseid'=>$courseid)));//, 'statusMSG'=> $statusMSG
        return ; // throw back
    }
    // Get on...
    // Call API to do the delete
    if (!xarModAPIFunc('courses',
            'admin',
            'deletecourse',
            array('courseid' => $courseid))) {
        return; // throw back
    }
    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarSessionSetVar('statusmsg',xarML('Course Deleted'));
    xarResponseRedirect(xarModURL('courses', 'admin', 'viewcourses'));
    // Return
    return true;
}
?>
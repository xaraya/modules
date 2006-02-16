<?php
/**
 * Delete a teacher
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * Delete a teacher, remove the teacher from a planned course
 * This function should only be used by an admin as for normal use the status of the student should be changed instead
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param  $ 'tid' the id of the teacher to be deleted
 * @param  $ 'planningid' the id of the planned course
 * @param  $ 'itemid' the id of the item to be deleted when another module calls this function
 * @param  $ 'confirm' confirm that this item can be deleted
 */
function courses_admin_deleteteacher($args)
{
    // Admin functions of this type can be called by other modules.
    extract($args);
    // Get parameters from whatever input we need.
    if (!xarVarFetch('tid',         'id', $tid)) return;
    if (!xarVarFetch('planningid',  'id', $planningid)) return;
    if (!xarVarFetch('objectid',    'id', $objectid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm',     'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $tid = $objectid;
    }
    // The user API function is called to get details.
    $item = xarModAPIFunc('courses',
        'user',
        'getteacher',
        array('tid' => $tid));
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Security check
    if (!xarSecurityCheck('AdminCourses', 1, 'Course', "All:$planningid:All")) { // Need the yearID here?
        return;
    }
    // Check for confirmation.
    if (empty($confirm)) {
        // No confirmation yet - display a suitable form to obtain confirmation
        // of this action from the user
        $data = xarModAPIFunc('courses', 'admin', 'menu');
        // Specify for which item you want confirmation
        $data['tid'] = $tid;
        $data['item'] = $item;
        $data['planningid'] = $item['planningid'];
        // Add some other data you'll want to display in the template
        $data['confirmtext'] = xarML('Confirm removing this person as a teacher from the course?');
        $data['itemid'] = xarML('Item ID');
        $data['planningidlabel'] = xarML('ID of course occurence');
        $data['namelabel'] = xarML('Name of teacher');
        $data['namevalue'] = xarVarPrepForDisplay(xarUserGetVar('name', $item['userid']));
        $data['confirmbutton'] = xarML('Confirm');
        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();
        // Return the template variables defined in this function
        return $data;
    }
    // Start delete
    if (!xarSecConfirmAuthKey()) return;
    // The API function is called.
    // The return value of the function is checked here, and if the function
    // succeeded then an appropriate message is posted.
    if (!xarModAPIFunc('courses',
            'admin',
            'deleteteacher',
            array('tid' => $tid))) {
        return; // throw back
    }
    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('courses', 'admin', 'teachers', array('planningid' => $planningid)));
    // Return
    return true;
}

?>

<?php
/**
 * Delete an participant
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
 * delete a participant, remove the participant from a planned course
 * This function should only be used by an admin as for normal use the status of the student should be changed instead
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param  $ 'sid' the id of the student to be deleted
 * @param  $ 'planningid' the id of the planned course
 * @param  $ 'itemid' the id of the item to be deleted when another module calls this function
 * @param  $ 'confirm' confirm that this item can be deleted
 */
function courses_admin_deleteparticipant($args)
{
    // Admin functions of this type can be called by other modules.
    extract($args);
    // Get parameters from whatever input we need.
    if (!xarVarFetch('sid',         'id', $sid)) return;
    if (!xarVarFetch('planningid',  'id', $planningid)) return;
    if (!xarVarFetch('objectid',    'id', $objectid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm',     'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $sid = $objectid;
    }
    // The user API function is called to get details.
    $item = xarModAPIFunc('courses',
        'user',
        'getparticipant',
        array('sid' => $sid));
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Security check
    if (!xarSecurityCheck('AdminCourses', 1, 'Course', "All:$planningid:All")) { //TODO: Include year here
        return;
    }
    // Check for confirmation.
    if (empty($confirm)) {
        // No confirmation yet - display a suitable form to obtain confirmation
        // of this action from the user
        $data = xarModAPIFunc('courses', 'admin', 'menu');
        // Specify for which item you want confirmation
        $data['sid'] = $sid;
        $data['item'] = $item;
        $data['planningid'] = $item['planningid'];
        // Add some other data you'll want to display in the template
        $data['confirmtext'] = xarML('Confirm removing this participant from the course?');
        $data['itemid'] = xarML('Item ID');
        $data['planningidlabel'] = xarML('ID of course occurence');
        $data['namelabel'] = xarML('Name of Participant');
        $data['namevalue'] = xarVarPrepForDisplay(xarUserGetVar('name', $item['userid']));
        $data['coursename'] =
        $data['confirmbutton'] = xarML('Confirm');
        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();
        // Return the template variables defined in this function
        return $data;
    }
    // Start delete
    if (!xarSecConfirmAuthKey()) return;
    // The API function is called.
    if (!xarModAPIFunc('courses',
            'admin',
            'deleteparticipant',
            array('sid' => $sid))) {
        return false; // throw back
    }
    // Return to participants for this planned course
    xarSessionSetVar('statusmsg','Participant deleted');
    xarResponseRedirect(xarModURL('courses', 'admin', 'participants', array('planningid' => $planningid)));
    // Return
    return true;
}
?>

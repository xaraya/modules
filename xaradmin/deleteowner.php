<?php
/*
 * File: $Id: $
 *
 * Newsletter 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2004 by the Xaraya Development Team
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/


/**
 * Delete an Newsletter owner
 *
 * @author Richard Cave
 * @param 'id' the id of the item to be deleted
 * @param 'confirm' confirm that this item can be deleted
 * @returns bool
 * @return true on success, false on failure
 */
function newsletter_admin_deleteowner()
{
    if(!xarSecurityCheck('AdminNewsletter')) return;

    // Get parameters from input
    if (!xarVarFetch('id', 'id', $id)) return;
    if (!xarVarFetch('confirm', 'int:0:1', $confirm, 0)) return;

    // The user API function is called
    $owner = xarModAPIFunc('newsletter',
                           'user',
                           'getowner',
                           array('id' => $id));

    // Check for exceptions
    if (!isset($owner) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Check for confirmation.
    if (!$confirm) {

        // get the admin menu
        $data = xarModAPIFunc('newsletter', 'admin', 'menu');

        // Specify for which owner you want confirmation
        $data['id'] = $id;

        // Data to display in the template
        $data['namevalue'] = xarVarPrepForDisplay($owner['name']);
        $data['confirmbutton'] = xarML('Confirm');

        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();

        // Return the template variables defined in this function
        return $data;
    }

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for deleting #(1) owner #(2)',
                    'Newsletter', xarVarPrepForDisplay($id));
        xarErrorSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // Remove the user from the old role
    $group = xarModAPIFunc('roles',
                           'user',
                           'removemember',
                            array('gid' => $owner['rid'],
                                  'uid' => $owner['id']));

    // Check return value
    if (!isset($group) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Remove the owner
    if (!xarModAPIFunc('newsletter',
                       'admin',
                       'deleteowner',
                       array('id' => $id))) {
        return; // throw back
    }

    xarSessionSetVar('statusmsg', xarML('User Deleted'));

    // Redirect
    xarResponseRedirect(xarModURL('newsletter', 'admin', 'viewusers'));

    // Return
    return true;
}

?>

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
 * Update an Newsletter owner
 *
 * @author Richard Cave
 * @param 'id' the id of the item to be updated
 * @param 'groupName' the name of the group
 * @param 'signature' the signature of the owner
 * @returns bool
 * @return true on success, false on failure
 */
function newsletter_admin_updateowner($args)
{
    // Confirm authorization code
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for updating #(1) item #(2)',
                    'Newsletter', xarVarPrepForDisplay($id));
        xarErrorSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // Get parameters from input
    if (!xarVarFetch('id', 'id', $id)) return;

    if (!xarVarFetch('groupName', 'str:1:', $groupName)) {
        xarErrorFree();
        $msg = xarML('You must select a group.');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    if (!xarVarFetch('signature', 'str:1:', $signature, '')) return;

    // Get the owner information
    $owner = xarModAPIFunc('newsletter',
                           'user',
                           'getowner',
                           array('id' => $id));

    // Check for exceptions
    if (!isset($owner) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Get the new role
    $role = xarFindRole($groupName);

    // See if the user group has changed
    if ($owner['rid'] != $role->uid) {

        // Remove the user from the old role
        $group = xarModAPIFunc('roles',
                               'user',
                               'removemember',
                                array('gid' => $owner['rid'],
                                      'uid' => $id));

        // Check return value
        if (!isset($group) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
            return; // throw back

        // Create user in new role
        $group = xarModAPIFunc('roles',
                               'user',
                               'addmember',
                                array('gid' => $role->uid,
                                      'uid' => $id));

        // Check return value
        if (!isset($group) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
            return; // throw back
    }

    // Call API function to update the owner
    if(!xarModAPIFunc('newsletter',
                      'admin',
                      'updateowner',
                      array('id' => $id,
                            'rid' => $role->uid,
                            'signature' => $signature))) {
        return; // throw back
    }

    xarSessionSetVar('statusmsg', xarML('Newsletter User Update'));

    // Redirect
    xarResponseRedirect(xarModURL('newsletter', 'admin', 'viewusers'));

    // Return
    return true;
}

?>

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
 * Create a new Newsletter owner
 *
 * @author Richard Cave
 * @param 'ownerid' id of the owner (uid in roles)
 * @param 'userGroup' group of the owner (rid in roles)
 * @returns bool
 * @return true on success, false on failure
 */
function newsletter_admin_createowner()
{
    // Confirm authorization key 
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for creating new #(1) item', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // Get parameters from the input
    if (!xarVarFetch('ownerId', 'id', $ownerId)) {
        xarErrorFree();
        $msg = xarML('You must select an owner name.');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    if (!xarVarFetch('userGroup', 'str:1:', $userGroup)) {
        xarErrorFree();
        $msg = xarML('You must select a group.');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Get the group id value
    $role = xarFindRole($userGroup);

    // Call create owner function API
    $newOwnerId = xarModAPIFunc('newsletter',
                           'admin',
                           'createowner',
                            array('id' => $ownerId,
                                  'rid' => $role->uid));

    // Check return value
    if (!isset($newOwnerId) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Create user in new role
    $group = xarModAPIFunc('roles',
                           'user',
                           'addmember',
                            array('gid' => $role->uid,
                                  'uid' => $ownerId));

    // Check return value
    if (!isset($group) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Success
    xarSessionSetVar('statusmsg', xarML('User Created'));

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('newsletter', 'admin', 'viewusers'));

    // Return
    return true;
}

?>

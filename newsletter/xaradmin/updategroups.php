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
 * Update groups
 *
 * @author Richard Cave
 * @param 'publisherGroup' the group to set publisher privileges
 * @param 'editorGroup' the group to set editor privileges
 * @param 'writerGroup' the group to set writer privileges
 * @param 'publisherMask' the publisher mask
 * @param 'editorMask' the editor mask
 * @param 'writerMask' the writer mask
 * @returns bool
 * @return true on success, false on failure
 */
function newsletter_admin_updategroups()
{
    // Confirm authorization code
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for updating #(1) configuration', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // Get parameters from input
    $default = xarModGetVar('newsletter', 'publisher');
    if (!xarVarFetch('publisherGroup', 'str:1:', $publisherGroup, $default)) return;

    $default = xarModGetVar('newsletter', 'editor');
    if (!xarVarFetch('editorGroup', 'str:1:', $editorGroup, $default)) return;

    $default = xarModGetVar('newsletter', 'writer');
    if (!xarVarFetch('writerGroup', 'str:1:', $writerGroup, $default)) return;

    $default = xarModGetVar('newsletter', 'publishermask');
    if (!xarVarFetch('publisherMask', 'str:1:', $publisherMask, $default)) return;

    $default = xarModGetVar('newsletter', 'editormask');
    if (!xarVarFetch('editorMask', 'str:1:', $editorMask, $default)) return;

    $default = xarModGetVar('newsletter', 'writermask');
    if (!xarVarFetch('writerMask', 'str:1:', $writerMask, $default)) return;

    // Make publisher group, roles, and privileges
    $publisherRole = xarModGetVar('newsletter', 'publisher');
    if ($publisherGroup != "**Don't Create**") {
        $role = xarFindRole($publisherRole);
        if (!$role) {
            xarMakeGroup($publisherRole);
        } else if ($role->getState() < 2) {
            // TODO! Change hard-coded values for getState() and setState()
            // TODO! to use roles definitions once available

            // Role has been de-activated, so activate
            if (!xarModAPIFunc('roles',
                               'admin',
                               'recall',
                                array('uid' => $role->getID(),
                                      'state' => 3))) {
                return false; // throw back
            }
        }
        
        // Assign publisher group to role
        xarMakeRoleMemberByName($publisherRole,$publisherGroup);

        // Assign privilege 
        if (!xarModAPIFunc('newsletter',
                           'admin',
                           'modifyprivilege',
                            array('type' => 'add',
                                  'mask' => $publisherMask, 
                                  'rolename' => $publisherRole))) {
            return false; // throw back
        }

        xarModSetVar('newsletter', 'publishermask', $publisherMask);
    }

    // Make editor group, roles, and privileges
    $editorRole = xarModGetVar('newsletter', 'editor');
    if ($editorGroup != "**Don't Create**") {
        $role = xarFindRole($editorRole);
        if (!$role) {
            xarMakeGroup($editorRole);
        } else if ($role->getState() < 2) {
            // TODO! Change hard-coded values for getState() and setState()
            // TODO! to use roles definitions once available

            // Role has been de-activated, so activate
            if (!xarModAPIFunc('roles',
                               'admin',
                               'recall',
                                array('uid' => $role->getID(),
                                      'state' => 3))) {
                return false; // throw back
            }
        }

        // Assign editor group to role
        xarMakeRoleMemberByName($editorRole,$editorGroup);

        // Assign privilege 
        if (!xarModAPIFunc('newsletter',
                           'admin',
                           'modifyprivilege',
                            array('type' => 'add',
                                  'mask' => $editorMask, 
                                  'rolename' => $editorRole))) {
            return false; // throw back
        }

        xarModSetVar('newsletter', 'editormask', $editorMask);
    }

    // Make writer group, roles, and privileges
    $writerRole = xarModGetVar('newsletter', 'writer');
    if ($writerGroup != "**Don't Create**") {
        $role = xarFindRole($writerRole);
        if (!$role) {
            xarMakeGroup($writerRole);
        } else if ($role->getState() < 2) {
            // TODO! Change hard-coded values for getState() and setState()
            // TODO! to use roles definitions once available

            // Role has been de-activated, so activate
            if (!xarModAPIFunc('roles',
                               'admin',
                               'recall',
                                array('uid' => $role->getID(),
                                      'state' => 3))) {
                return false; // throw back
            }
        }
    
        // Assign writer group to role
        xarMakeRoleMemberByName($writerRole,$writerGroup);

        // Assign privilege 
        if (!xarModAPIFunc('newsletter',
                           'admin',
                           'modifyprivilege',
                            array('type' => 'add',
                                  'mask' => $writerMask, 
                                  'rolename' => $writerRole))) {
            return false; // throw back
        }

        xarModSetVar('newsletter', 'writermask', $writerMask);
    }

    // Update creategroups var
    xarModSetVar('newsletter', 'creategroups', 1);

    xarModCallHooks('module','viewusers','newsletter',
                    array('module' => 'newsletter'));

    // Redirect
    xarResponseRedirect(xarModURL('newsletter', 'admin', 'viewusers'));

    // Return
    return true;
}

?>

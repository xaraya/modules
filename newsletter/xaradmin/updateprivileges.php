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
 * Update privileges
 *
 * @author Richard Cave
 * @param 'publisherMask' the new publisher mask
 * @param 'editorMask' the new editor mask
 * @param 'writerMask' the new writer mask
 * @returns bool
 * @return true on success, false on failure
 */
function newsletter_admin_updateprivileges()
{
    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for updating #(1) configuration', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // Get parameters from input
    $default = xarModGetVar('newsletter', 'publishermask');
    if (!xarVarFetch('publisherMask', 'str:1:', $publisherMask, $default)) return;

    $default = xarModGetVar('newsletter', 'editormask');
    if (!xarVarFetch('editorMask', 'str:1:', $editorMask, $default)) return;

    $default = xarModGetVar('newsletter', 'writermask');
    if (!xarVarFetch('writerMask', 'str:1:', $writerMask, $default)) return;


    if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
    // Set masks if they changed
    $oldPublisherMask = xarModGetVar('newsletter', 'publishermask');
    if ($publisherMask != $oldPublisherMask) {
        $publisherRole = xarModGetVar('newsletter', 'publisher');

        // Remove old privilege 
        if (!xarModAPIFunc('newsletter',
                           'admin',
                           'modifyprivilege',
                           array('type' => 'remove',
                                  'mask' => $oldPublisherMask, 
                                  'rolename' => $publisherRole))) {
            return false; // throw back
        }

        // Assign new privilege 
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

    $oldEditorMask = xarModGetVar('newsletter', 'editormask');
    if ($editorMask != $oldEditorMask) {
        $editorRole = xarModGetVar('newsletter', 'editor');

        // Remove old privilege 
        if (!xarModAPIFunc('newsletter',
                           'admin',
                           'modifyprivilege',
                            array('type' => 'remove',
                                  'mask' => $oldEditorMask, 
                                  'rolename' => $editorRole))) {
            return false; // throw back
        }

        // Assign new privilege 
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

    $oldWriterMask = xarModGetVar('newsletter', 'writermask');
    if ($writerMask != $oldWriterMask) {
        $writerRole = xarModGetVar('newsletter', 'writer');

        // Remove old privilege 
        if (!xarModAPIFunc('newsletter',
                           'admin',
                           'modifyprivilege',
                            array('type' => 'remove',
                                  'mask' => $oldWriterMask, 
                                  'rolename' => $writerRole))) {
            return false; // throw back
        }

        // Assign new privilege 
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

    xarModCallHooks('module','modifyprivileges','newsletter',
                    array('module' => 'newsletter'));

    // Redirect
    xarResponseRedirect(xarModURL('newsletter', 'admin', 'modifyprivileges'));

    // Return
    return true;
}

?>

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
 * Delete an Newsletter subscription
 *
 * @public
 * @author Richard Cave
 * @param 'uid' the uid of the subscription
 * @param 'confirm' confirm that this item can be deleted
 * @returns array
 * @return $data
 */
function newsletter_admin_deletesubscription($args)
{
    // Extract args
    extract ($args);

    // Security check
    if(!xarSecurityCheck('DeleteNewsletter')) return;

    // Get parameters from input
    if (!xarVarFetch('uid', 'int:1:', $uid, 0)) return;
    if (!xarVarFetch('pid', 'int:1:', $pid, 0)) return;
    if (!xarVarFetch('confirm', 'int:0:1', $confirm, 0)) return;

    // The user API function is called
    $subscription = xarModAPIFunc('newsletter',
                                  'user',
                                  'getsubscription',
                                  array('uid' => $uid));

    // Check for exceptions
    if (!isset($subscription) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Check for confirmation.
    if (!$confirm) {

        // Get the admin menu
        $data = xarModAPIFunc('newsletter', 'admin', 'menu');

        // Specify for which item you want confirmation
        $data['uid'] = $uid;
        $data['pid'] = $pid;
        $data['confirmbutton'] = xarML('Confirm');

        // Data to display in the template
        $data['name'] = xarVarPrepForDisplay($subscription['name']);
        $data['uname'] = xarVarPrepForDisplay($subscription['uname']);
        $data['email'] = xarVarPrepForDisplay($subscription['email']);

        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();

        // Return the template variables defined in this function
        return $data;
    }

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for deleting #(1) item #(2)',
                    'Newsletter', xarVarPrepForDisplay($uid));
        xarErrorSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // The API function is called
    if (!xarModAPIFunc('newsletter',
                       'admin',
                       'deletesubscription',
                       array('uid' => $uid))) {
        return; // throw back
    }

    xarSessionSetVar('statusmsg', xarML('Subscription Deleted'));

    // Redirect
    xarResponseRedirect(xarModURL('newsletter', 'admin', 'searchsubscription'));
}

?>

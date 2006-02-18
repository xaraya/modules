<?php
/**
 * Newsletter
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
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
 * @param 'uid' the user id of the subscription
 * @param 'pid' the publication id of the subscription
 * @param 'confirm' confirm that this item can be deleted
 * @return array $data
 */
function newsletter_user_deletesubscription($args)
{
    // Extract args
    extract ($args);

    // Security check
    if(!xarSecurityCheck('DeleteNewsletter')) return;

    // Get parameters from input
    if (!xarVarFetch('uid', 'id', $uid, 0)) return;
    if (!xarVarFetch('pid', 'id', $pid, 0)) return;
    if (!xarVarFetch('confirm', 'int:0:1', $confirm, 0)) return;

    // The user API function is called
    $subscription = xarModAPIFunc('newsletter',
                                  'user',
                                  'getsubscription',
                                  array('uid' => $uid,
                                        'pid' => $pid));

    // Check for exceptions
    if (!isset($subscription) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
        return; // throw back

    // Check for confirmation.
    if (!$confirm) {

        // Get the user menu
        $data = xarModAPIFunc('newsletter', 'user', 'menu');

        // Specify for which item you want confirmation
        $data['uid'] = $uid;
        $data['pid'] = $pid;
        $data['confirmbutton'] = xarML('Confirm');

        // Data to display in the template
        $data['name'] = xarVarPrepForDisplay($subscription['name']);
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
                    'Newsletter', xarVarPrepForDisplay($id));
        xarErrorSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // The API function is called
    if (!xarModAPIFunc('newsletter',
                       'user',
                       'deletesubscription',
                       array('uid' => $uid,
                             'pid' => $pid))) {
        return; // throw back
    }

    xarSessionSetVar('statusmsg', xarML('Subscription Deleted'));

    // Redirect
    xarResponseRedirect(xarModURL('newsletter', 'user', 'modifysubscription'));
}

?>

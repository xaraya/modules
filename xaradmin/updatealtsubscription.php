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
 * Update an Newsletter alternative subscription
 *
 * @public
 * @author Richard Cave
 * @param 'id' id of the subscription
 * @param 'name' name of the subscription
 * @param 'email' email address of the subscription
 * @param 'htmlmail' send mail html or text (0 = text, 1 = html)
 * @param 'pids' the publication ids
 * @returns bool
 * @return true on success, false on failure
 */
function newsletter_admin_updatealtsubscription()
{
    // Confirm authorization code
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for updating #(1) item #(2) in function #(3)',
                    'Newsletter', xarVarPrepForDisplay($id), 'newsletter_admin_updatealtsubscription');
        xarErrorSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return false;
    }

    // Get parameters from the input
    if (!xarVarFetch('id', 'int:1:', $id)) return;
    if (!xarVarFetch('name', 'str:1:', $name, '')) return;

    if (!xarVarFetch('email', 'str:1:', $email)) {
        xarErrorFree();
        $msg = xarML('You must provide an email address.');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return false;
    }

    if (!xarVarFetch('pids', 'array:1:', $pids, array())) return;
    if (!xarVarFetch('htmlmail', 'int:0:1:', $htmlmail, 0)) return;

    // Get the current alt subscription
    $subscription = xarModAPIFunc('newsletter',
                                  'user',
                                  'getaltsubscription',
                                  array('id' => $id));

    if (!$subscription)
        return false; // throw back

    // Quick and dirty - delete all subscriptions for the user
    if (!xarModAPIFunc('newsletter',
                       'admin',
                       'deletealtsubscription',
                        array('id' => $id,
                              'email' => $subscription['email']))) {
        return false; // throw back
    }

    // And create again...
    if (!empty($pids)) {
        foreach ($pids as $pid) {
            // Call create subscription function API
            $item =xarModAPIFunc('newsletter',
                                 'admin',
                                 'createaltsubscription',
                                  array('name' => $name,
                                        'email' => $email,
                                        'pid' => $pid,
                                        'htmlmail' => $htmlmail));

            if (!$item)
                return false; // throw back
        }
    }

    xarSessionSetVar('statusmsg', xarML('Newsletter Subscription Update'));

    // Redirect
    xarResponseRedirect(xarModURL('newsletter', 'admin', 'viewsubscription'));

    // Return
    return true;
}

?>

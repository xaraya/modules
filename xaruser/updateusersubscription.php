<?php
/*
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
 * Update an Newsletter subscription
 *
 * @public
 * @author Richard Cave
 * @param 'uid' the user id of the subscription to be modified
 * @param 'pids' the publication ids
 * @param 'htmlmail' send mail html or text (0 = text, 1 = html)
 * @returns bool
 * @return true on success, false on failure
 */
function newsletter_user_updateusersubscription($args)
{
    extract($args);

    // Confirm authorization code
   /*
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for updating #(1) item #(2) in function #(3)',
                    'Newsletter', xarVarPrepForDisplay($id), 'newsletter_user_updatesubscription');
        xarErrorSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }
    */
    include('c:\wamp\www\phpDump.class.php');
    dump($args);
    // Get parameters from the input
    if (!xarVarFetch('uid', 'id', $uid)) return;
    if (!xarVarFetch('pids', 'array:1:', $pids, array())) return;
    if (!xarVarFetch('htmlmail', 'int:0:1:', $htmlmail, 0)) return;



    // Quick and dirty - delete all subscriptions for the user
    if (!xarModAPIFunc('newsletter',
                       'user',
                       'deletesubscription',
                        array('id' => 0, // fake
                              'uid' => $uid))) {
        return false; // throw back
    }

    // Check if any publications were selected
    if (!empty($pids)) {
        // And create again...
        foreach ($pids as $pid) {
            // Call create subscription function API
            $item =xarModAPIFunc('newsletter',
                                 'user',
                                 'createsubscription',
                                  array('uid' => $uid,
                                        'pid' => $pid,
                                        'htmlmail' => $htmlmail));

            if (!$item)
                return false; // throw back
        }
   }

    // Return
    return true;
}

?>

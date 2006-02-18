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
 * Create a new Newsletter subscription
 *
 * @public
 * @author Richard Cave
 * @param 'pids' the publication ids
 * @param 'htmlmail' send mail html or text (0 = text, 1 = html)
 * @return bool true on success, false on failure
 */
function newsletter_user_createsubscription()
{
    // Get parameters from the input
    if (!xarVarFetch('pids', 'array:1:', $pids, array())) return;
    if (!xarVarFetch('htmlmail', 'int:0:1:', $htmlmail, 0)) return;

    // Get the current user
    if (xarUserIsLoggedIn())
        $uid = xarUserGetVar('uid');
    else
        return false;

    // Check if any publications were selected
    if (!empty($pids)) {
        foreach ($pids as $pid) {
            // Call create subscription function API
            $item = xarModAPIFunc('newsletter',
                                  'user',
                                  'createsubscription',
                                   array('uid' => $uid,
                                         'pid' => $pid,
                                         'htmlmail' => $htmlmail));
            if ($item)
                xarSessionSetVar('statusmsg', xarML('Subscription Created'));
        }

        // Redirect the user
        xarResponseRedirect(xarModURL('newsletter', 'user', 'modifysubscription'));
    } else {
        // Redirect the user
        xarResponseRedirect(xarModURL('newsletter', 'user', 'newsubscription'));
    }

    // Return
    return true;
}

?>

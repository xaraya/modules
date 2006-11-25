<?php
/**
 * Xaraya HTML Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage HTML Module
 * @link http://xaraya.com/index.php/release/779.html
 * @author John Cox
 */

/**
 * Update the allowed HTML
 *
 * @public
 * @author John Cox
 * @author Richard Cave
 * @param $args['tags'] an array of the cids and allowed value of the html tags
 * @throws MISSING_DATA
 */
function html_admin_updateset()
{
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    // Security Check
    if(!xarSecurityCheck('AdminHTML')) return;

    // Get parameters from the input
    if (!xarVarFetch('tags', 'array:1:', $tags)) {
        $msg = xarML('No HTML tags were selected.');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return false;
    }

    // Initialize array for config vars
    $allowedhtml = array();

    // Update HTML tags
    foreach ($tags as $cid=>$allowed) {
        // Get the cid of the htmltag
        $thistag = xarModAPIFunc('html',
                                 'user',
                                 'gettag',
                                 array('cid' => $cid));

        if ($thistag) {
            $tag = $thistag['tag'];

            // Check if update is necessary
            if ($thistag['allowed'] != $allowed) {
                // Update
                if (!xarModAPIFunc('html',
                                   'admin',
                                   'update',
                                   array('cid' => $cid,
                                         'allowed' => $allowed)))
                    return false;
            }

            // If this is an html tag, then
            // also update the config vars array
            if ($thistag['type'] == 'html') {
                $allowedhtml[$tag] = $allowed;
            }
        }
    }

    // Set config vars
    xarConfigSetVar('Site.Core.AllowableHTML', $allowedhtml);

    // Redirect back to set
    xarResponseRedirect(xarModURL('html', 'admin', 'set'));

    return true;
}

?>

<?php
/**
 * File: $Id$
 *
 * Xaraya HTML Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage HTML Module
 * @author John Cox
*/

/**
 * Update the allowed HTML
 *
 * @public
 * @author John Cox
 * @purifiedby Richard Cave 
 * @param $args['htmltags'] an array of the cids and allowed value of the html tags
 * @raise MISSING_DATA
 */
function html_admin_updateset()
{
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    // Security Check
	if(!xarSecurityCheck('AdminHTML')) return;

    // Get parameters from the input
    if (!xarVarFetch('htmltags', 'array:1:', $htmltags)) {
        $msg = xarML('No HTML tags were selected.');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return false;
    }

    // Initialize array for config vars
    $allowedhtml = array();

    // Update HTML tags
    foreach ($htmltags as $tag=>$allowed) {
        // Get the cid of the htmltag
        $html = xarModAPIFunc('html',
                              'user',
                              'getbytag',
                              array('tag' => $tag));

        if ($html) {
            // Check if update is necessary
            if ($html['allowed'] != $allowed) {
                // Update
                if (!xarModAPIFunc('html',
                                   'admin',
                                   'update',
                                   array('cid' => $html['cid'],
                                         'allowed' => $allowed))) 
                    return false;
            }

            // Add to config vars array
            $allowedhtml[$tag] = $allowed;
        }
    }

    // Set config vars
    xarConfigSetVar('Site.Core.AllowableHTML', $allowedhtml);

    // Redirect back to set
    xarResponseRedirect(xarModURL('html', 'admin', 'set'));

    return true;
}

?>

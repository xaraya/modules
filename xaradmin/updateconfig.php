<?php
/**
 * Standard Utility function pass individual menu items to the main menu
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarDPLink Module
 * @link http://xaraya.com/index.php/release/591.html
 * @author xarDPLink Module Development Team
 */

function xardplink_admin_updateconfig()
{
    // Get parameters from whatever input we need.
    $_loc = pnVarCleanFromInput('url');
    $_window = pnVarCleanFromInput('use_window');
    $_wrap = pnVarCleanFromInput('use_postwrap');


    // Confirm authorisation code.
    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', _BADAUTHKEY);
        pnRedirect(pnModURL('xardplink', 'admin', ''));
        return true;
    }

    // Update module variables.
    pnModSetVar('xardplink', 'url', $_loc);
    pnModSetVar('xardplink', 'use_window', $_window);
    pnModSetVar('xardplink', 'use_postwrap', $_wrap);

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    pnRedirect('admin.php');

    // Return
    return true;
}
?>

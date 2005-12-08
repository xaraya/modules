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

function xardplink_admin_updateconfig($args)
{
    extract($args);
    if (!xarVarFetch('use_window',         'checkbox', $use_window, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('url', 'str',      $url, '/dotproject', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('use_wrap',    'checkbox', $use_wrap, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('aliasname',    'str:1:',   $aliasname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('modulealias',  'checkbox', $modulealias,false,XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;

    // Update module variables.
    xarModSetVar('xardplink', 'url', $url);
    xarModSetVar('xardplink', 'use_window', $use_window);
    xarModSetVar('xardplink', 'use_wrap', $use_wrap);

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('xardplink', 'admin', 'modifyconfig'));

    // Return
    return true;
}
?>

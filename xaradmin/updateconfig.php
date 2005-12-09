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
    if (!xarVarFetch('use_window',  'checkbox', $use_window, false,         XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('url',         'str',      $url,        '/dotproject', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('use_wrap',    'checkbox', $use_wrap,   false,         XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;

    if (!($use_wrap) && !($use_window)) {
        xarSessionSetVar('statusmsg', xarML('Please choose at least one option'));
        return xarModFunc('xardplink', 'admin', 'modifyconfig',
                          array('use_window' => $use_window,
                                'url' => $url,
                                'use_wrap' => $use_wrap));
    } elseif (($use_wrap) && ($use_window)) {
        xarSessionSetVar('statusmsg', xarML('You cannot choose both option, please choose only one'));
        return xarModFunc('xardplink', 'admin', 'modifyconfig',
                          array('use_window' => $use_window,
                                'url' => $url,
                                'use_wrap' => $use_wrap));
    } else {
        // Update module variables.
        xarModSetVar('xardplink', 'url', $url);
        xarModSetVar('xardplink', 'use_window', $use_window);
        xarModSetVar('xardplink', 'use_wrap', $use_wrap);
        xarSessionSetVar('statusmsg', xarML('Configuration updated'));
    }
    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('xardplink', 'admin', 'modifyconfig'));

    // Return
    return true;
}
?>

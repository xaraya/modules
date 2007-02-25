<?php
/**
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.zwiggybo.com
 *
 * @subpackage shouter
 * @link http://xaraya.com/index.php/release/236.html
 * @author Neil Whittaker
 */

/**
 * Delete all shouts
 *
 * @return bool
 */
function shouter_admin_deleteall($args)
{
    extract($args);

    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarSecurityCheck('DeleteAllShouter')) return;

    $items = xarModAPIFunc('shouter', 'user', 'getall');

    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (empty($confirm)) {
        // No confirmation yet
        $data = xarModAPIFunc('shouter', 'admin', 'menu');

        $data['confirmtext'] = xarML('Click the button to delete the shout');
        $data['itemid'] = xarML('Item ID');
        $data['namelabel'] = xarML('Shouter Name');

        $data['confirmbutton'] = xarML('Delete all Shouts!');

        $data['authid'] = xarSecGenAuthKey();

        return $data;
    }

    if (!xarSecConfirmAuthKey()) return;

    if (!xarModAPIFunc('shouter', 'admin', 'deleteall')) {
        return;
    }

    xarResponseRedirect(xarModURL('shouter', 'admin', 'view'));
}
?>
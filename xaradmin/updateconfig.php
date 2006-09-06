<?php
/**
 * Standard function to update module configuration parameters
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage flickring
 * @author Johnny Robeson
 */

/**
 * Standard function to update module configuration parameters
 *
 */
function flickring_admin_updateconfig()
{
    if (!xarVarFetch('key',    'str:1:',   $key, xarModGetVar('flickring', 'key'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('secret', 'str:1:',   $secret, xarModGetVar('flickring', 'secret'), XARVAR_NOT_REQUIRED)) return;


    if (!xarSecConfirmAuthKey()) return;

    xarModSetVar('flickring', 'key', $key);
    xarModSetVar('flickring', 'secret', $secret);

    xarResponseRedirect(xarModURL('flickring', 'admin', 'modifyconfig'));

    return true;
}
?>
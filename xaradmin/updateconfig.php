<?php
/**
 * Update configuration parameters of the module with information passed back by the modification form
 * 
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
 */

/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function release_admin_updateconfig()
{
    // Get parameters from whatever input we need.
    if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemsperpage', 'int', $itemsperpage, 20, XARVAR_NOT_REQUIRED)) return;
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;
    // Update module variables.
    xarModSetVar('release', 'SupportShortURLs', $shorturls);
    xarModSetVar('release', 'itemsperpage', $itemsperpage);
    xarModCallHooks('module','updateconfig','release',
                   array('module' => 'release'));

    xarResponseRedirect(xarModURL('release', 'admin', 'modifyconfig'));

    // Return
    return true;
}

?>
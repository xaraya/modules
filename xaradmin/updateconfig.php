<?php
/**
 * Subitems module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Subitems Module
 * @link http://xaraya.com/index.php/release/9356.html
 * @author Subitems Module Development Team
 */
/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function subitems_admin_updateconfig()
{
    // Get parameters
    if (!xarVarFetch('itemsperpage', 'str:1:', $itemsperpage, '10', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarSecConfirmAuthKey()) return;
    xarModSetVar('subitems', 'SupportShortURLs', $shorturls);
    xarModCallHooks('module','updateconfig','subitems',
                   array('module' => 'subitems','itemtype' => 1));
    xarResponseRedirect(xarModURL('subitems', 'admin', 'modifyconfig'));
    // Return
    return true;
}
?>
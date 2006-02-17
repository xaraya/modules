<?php
/**
 * Update configuration parameters of the module with information passed back by the modification form
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @author MichelV.
 */
/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 * @return bool
 */
function sigmapersonnel_admin_updateconfig()
{
    // Get parameters from whatever input we need.
    if (!xarVarFetch('bold', 'checkbox', $bold, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemsperpage', 'int', $itemsperpage, 10, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('OnCallID', 'id', $OnCallID, 1, XARVAR_NOT_REQUIRED)) return;
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;
    // Update module variables.
    xarModSetVar('sigmapersonnel', 'bold', $bold);
    xarModSetVar('sigmapersonnel', 'itemsperpage', $itemsperpage);
    xarModSetVar('sigmapersonnel', 'SupportShortURLs', $shorturls);
    xarModSetVar('sigmapersonnel', 'OnCallID', $OnCallID);
    xarModCallHooks('module','updateconfig','sigmapersonnel',
                   array('module' => 'sigmapersonnel'));

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('sigmapersonnel', 'admin', 'modifyconfig'));

    // Return
    return true;
}

?>

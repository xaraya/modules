<?php
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
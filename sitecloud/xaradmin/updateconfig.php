<?php
/**
 * update configuration
 */
function sitecloud_admin_updateconfig()
{
    if (!xarVarFetch('itemsperpage','int:1:',$itemsperpage,'20',XARVAR_NOT_REQUIRED)) return;
    if (!xarSecConfirmAuthKey()) return;
    // Security Check
	if(!xarSecurityCheck('Adminsitecloud')) return;
    xarModSetVar('sitecloud', 'itemsperpage', $itemsperpage);
    xarModCallHooks('module','updateconfig','sitecloud', array('module' => 'sitecloud'));
    xarResponseRedirect(xarModURL('sitecloud', 'admin', 'modifyconfig'));
    return true;
}
?>

<?php
/**
 * update configuration
 */
function headlines_admin_updateconfig()
{
    if (!xarSecConfirmAuthKey()) return;
	if(!xarSecurityCheck('AdminHeadlines')) return;
    if (!xarVarFetch('itemsperpage', 'str:1:', $itemsperpage, '20', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('importpubtype', 'id', $importpubtype, 1, XARVAR_NOT_REQUIRED)) return;
    xarModSetVar('headlines', 'itemsperpage', $itemsperpage);
    xarModSetVar('headlines', 'SupportShortURLs', $shorturls);
    xarModSetVar('headlines', 'importpubtype', $importpubtype);
    xarModCallHooks('module','updateconfig','headlines', array('module' => 'headlines'));
    xarResponseRedirect(xarModURL('headlines', 'admin', 'modifyconfig'));
    return true;
}
?>

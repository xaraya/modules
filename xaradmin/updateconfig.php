<?php
/**
 * update configuration
 */
function ephemerids_admin_updateconfig()
{
    if (!xarVarFetch('itemsperpage','int:1:',$itemsperpage, 10)) return;
    if (!xarSecConfirmAuthKey()) return;
    if(!xarSecurityCheck('AdminEphemerids')) return;
    xarModSetVar('ephemerids', 'itemsperpage', $itemsperpage);
    xarResponseRedirect(xarModURL('ephemerids', 'admin', 'modifyconfig'));
    return true;
}

?>
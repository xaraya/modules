<?php

function vendors_admin_main()
{
    if(!xarSecurityCheck('AdminVendors')) return;

    if (xarModGetVar('modules', 'disableoverview') == 0) {
        return array();
    } else {
        xarResponseRedirect(xarModURL('vendors', 'admin', 'view'));
    }
    // success
    return true;
}
?>
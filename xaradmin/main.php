<?php

function customers_admin_main()
{
    if(!xarSecurityCheck('AdminCustomers')) return;

    if (xarModGetVar('modules', 'disableoverview') == 0) {
        return array();
    } else {
        xarResponseRedirect(xarModURL('customers', 'admin', 'customers'));
    }
    // success
    return true;
}
?>
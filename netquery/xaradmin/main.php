<?php

function netquery_admin_main()
{
    if (!xarSecurityCheck('EditNetquery')) return;
    if (xarModGetVar('adminpanels', 'overview') == 0) {
        return array();
    } else {
        xarResponseRedirect(xarModURL('netquery', 'admin', 'modifyconfig'));
    }
    return true;
}
?>

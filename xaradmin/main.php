<?php
function window_admin_main()
{
    if (!xarSecurityCheck('AdminWindow')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0) {
        return array();
    } else {
        return xarResponseRedirect(xarModURL('window', 'admin', 'general'));
    }

}
?>
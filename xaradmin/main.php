<?php
function pop3gateway_admin_main()
{
    // Security Check
    if(!xarSecurityCheck('AdminPOP3Gateway')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0){
        // Return the output
        return array();
    } else {
        xarResponseRedirect(xarModURL('pop3gateway', 'admin', 'modifyconfig'));
    }
    // success
    return true;
}
?>

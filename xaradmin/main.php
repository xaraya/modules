<?php

/**
 * Add a standard screen upon entry to the module.
 * @returns output
 * @return output with Autolinks Menu information
 */
function autolinks_admin_main()
{
    // Security Check
    if (!xarSecurityCheck('EditAutolinks')) return;
    if (xarModGetVar('adminpanels', 'overview') == 0) {
        // Return the output
        return array();
    } else {
        xarResponseRedirect(xarModURL('autolinks', 'admin', 'view'));
    }
    // success
    return true;
}

?>
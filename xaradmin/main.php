<?php

/**
 * Add a standard screen upon entry to the module.
 * @returns output
 * @return output with Autolinks Menu information
 */
function stats_admin_main()
{
    // Security Check
    if(!xarSecurityCheck('AdminStats')) return;
    if (xarModGetVar('adminpanels', 'overview') == 0){
        // Return the output
        return array();
    } else {
        xarResponseRedirect(xarModURL('stats', 'admin', 'modifyconfig'));
    }
    // success
    return true;
}

?>
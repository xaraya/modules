<?php

/**
 * Add a standard screen upon entry to the module.
 * @returns output
 * @return output with xarbb Menu information
 */
function xarbb_admin_main()
{
    // Security Check
    if(!xarSecurityCheck('EditxarBB')) return;
    if (xarModGetVar('adminpanels', 'overview') == 0){
        // Return the output
        return array();
    } else {
        xarResponseRedirect(xarModURL('xarbb', 'admin', 'view'));
    }
    // success
    return true;
}

?>
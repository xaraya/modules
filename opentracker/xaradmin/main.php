<?php

/**
 * Add a standard screen upon entry to the module.
 * @returns output
 * @return output with Opentracker Menu information
 */
function opentracker_admin_main()
{
    // Security Check
    if(!xarSecurityCheck('AdminOpentracker')) return;
    if (xarModGetVar('adminpanels', 'overview') == 0){
        // Return the output
        return array();
    } else {
        xarResponseRedirect(xarModURL('opentracker', 'admin', 'modifyconfig'));
    }
    // success
    return true;
}

?>

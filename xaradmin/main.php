<?php

/**
 * the main administration function
 * 
 * @author mikespub
 * @access public 
 * @param no $ parameters
 * @return true on success or void on falure
 * @throws XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION'
 */
function workflow_admin_main()
{ 
    // Security Check
    if (!xarSecurityCheck('AdminWorkflow')) return;

        xarResponseRedirect(xarModURL('workflow', 'admin', 'processes'));
    // success
    return true;
}

?>

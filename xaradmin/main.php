<?php

/**
 * the main administration function
 * 
 * @author Flavio Botelho <nuncanada@xaraya.com>
 * @access public 
 * @param no $ parameters
 * @return true on success or void on falure
 * @throws XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION'
 */
function cachesecurity_admin_main()
{ 
    // Security Check
    if (!xarSecurityCheck('AdminCacheSecurity')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0) {
        // Return the output
        return array();
    } else {
        xarResponseRedirect(xarModURL('cachesecurity', 'admin', 'view'));
    } 
    // success
    return true;
} 

?>
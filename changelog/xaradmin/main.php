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
function changelog_admin_main()
{ 
    // Security Check
    if (!xarSecurityCheck('AdminChangeLog')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0) {
        // Return the output
        return array();
    } else {
        xarResponseRedirect(xarModURL('changelog', 'admin', 'modifyconfig'));
    } 
    // success
    return true;
} 

?>

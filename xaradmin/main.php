<?php

/**
 * the main administration function
 * 
 * @author Vassilis Stratigakis 
 * @access public 
 * @param no $ parameters
 * @return true on success or void on falure
 * @throws XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION'
 */
function userpoints_admin_main()
{ 
    // Security Check
    if (!xarSecurityCheck('AdminUserpoints')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0) {
        // Return the output
        return array();
    } else {
        xarResponseRedirect(xarModURL('userpoints', 'admin', 'modifyconfig'));
    } 
    // success
    return true;
} 

?>
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
function keywords_admin_main()
{ 
    // Security Check
    if (!xarSecurityCheck('AdminKeywords')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0) {
        // Return the output
        return array();
    } else {
        xarResponseRedirect(xarModURL('keywords', 'admin', 'modifyconfig'));
    } 
    // success
    return true;
} 

?>

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
function xlink_admin_main()
{ 
    // Security Check
    if (!xarSecurityCheck('AdminXLink')) return;

        xarResponseRedirect(xarModURL('xlink', 'admin', 'modifyconfig'));
    // success
    return true;
}

?>

<?php

/**
 * the main administration function
 * 
 * @author jsb | mikespub
 * @access public 
 * @param no $ parameters
 * @return true on success or void on falure
 * @throws XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION'
 */
function xarcachemanager_admin_main()
{ 
    // Security Check
    if (!xarSecurityCheck('AdminXarCache')) return;

        xarResponseRedirect(xarModURL('xarcachemanager', 'admin', 'modifyconfig'));
    // success
    return true;
} 

?>
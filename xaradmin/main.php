<?php

/**
 * the main administration function
 */
function scheduler_admin_main()
{ 
    if (!xarSecurityCheck('AdminScheduler')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0) {
        return array();
    } else {
        xarResponseRedirect(xarModURL('scheduler', 'admin', 'modifyconfig'));
    }
    return true;
} 

?>

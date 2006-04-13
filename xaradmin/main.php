<?php

/**
 * the main administration function
 */
function scheduler_admin_main()
{ 
    if (!xarSecurityCheck('AdminScheduler')) return;

        xarResponseRedirect(xarModURL('scheduler', 'admin', 'modifyconfig'));

    return true;
} 

?>

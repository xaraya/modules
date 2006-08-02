<?php

/**
 * the main administration function
 */
function window_admin_main()
{
    if(!xarSecurityCheck('AdminWindow')) return;

    if (xarModGetVar('modules', 'disableoverview') == 0){
        // Return the output
        return array();
    } else {
        xarResponseRedirect(xarModURL('window', 'admin', 'modifyconfig'));
    }

    return true;
}
?>
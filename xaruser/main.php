<?php

/**
 * the main user function
 */
function window_user_main()
{

    // Security check
    if(!xarSecurityCheck('ViewWindow')) return;

    if (xarModGetVar('modules', 'disableoverview') == 0){
        // Return the output
        return array();
    } else {
        xarResponseRedirect(xarModURL('window', 'user', 'display'));
    }

    return true;
}

?>
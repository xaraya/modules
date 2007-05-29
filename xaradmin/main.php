<?php
/**
 * Overview Menu
 */
function trackback_admin_main()
{
    if(!xarSecurityCheck('Addtrackback')){
        return;
    }
    if (xarModVars::get('modules', 'disableoverview') == 0){
        // Return the output
        return array();
    } else {
        xarResponseRedirect(xarModURL('trackback', 'admin', 'new'));
    }
    // success
    return true;
}
?>
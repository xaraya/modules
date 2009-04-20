<?php
/**
 * Main admin GUI function, entry point
 *
 */

    function xarayatesting_admin_main()
    {
        if(!xarSecurityCheck('EditXarayatesting')) return;

        if (xarModVars::get('modules', 'disableoverview') == 0){
            return xarTplModule('xarayatesting','admin','overview');
        } else {
            xarResponse::Redirect(xarModURL('xarayatesting', 'admin', 'view'));
            return true;
        }
    }
?>
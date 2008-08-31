<?php
/**
 * Main admin GUI function, entry point
 *
 */

    function xarayatesting_admin_main()
    {
        if(!xarSecurityCheck('AdminXarayatesting')) return;

        if (!xarModVars::get('modules', 'disableoverview') == 0) {
            xarResponseRedirect(xarModURL('xarayatesting', 'admin', 'mastertables'));
        }
        return array();
    }
?>
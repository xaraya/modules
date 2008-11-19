<?php
/**
 * Main admin GUI function, entry point
 *
 */

    function karma_admin_main()
    {
        if(!xarSecurityCheck('AdminKarma')) return;

        if (xarModVars::get('modules', 'disableoverview') == 0) {
            return array();
        } else {
            xarResponseRedirect(xarModURL('karma', 'admin', 'modifyconfig'));
        }
        // success
        return true;
    }
?>
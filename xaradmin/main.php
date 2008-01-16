<?php
/**
 * Main admin GUI function, entry point
 *
 */

    function foo_admin_main()
    {
        if(!xarSecurityCheck('AdminFoo')) return;

        if (xarModVars::get('modules', 'disableoverview') == 0) {
            return array();
        } else {
            xarResponseRedirect(xarModURL('foo', 'admin', 'modifyconfig'));
        }
        // success
        return true;
    }
?>
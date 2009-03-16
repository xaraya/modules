<?php
/**
 * Main admin GUI function, entry point
 *
 */

    function ckeditor_admin_main()
    {
        if(!xarSecurityCheck('AdminCKEditor')) return;

        if (xarModVars::get('modules', 'disableoverview') == 0) {
            return array();
        } else {
            xarResponseRedirect(xarModURL('ckeditor', 'admin', 'modifyconfig'));
        }
        // success
        return true;
    }
?>
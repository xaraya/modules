<?php
/**
 * Main admin GUI function, entry point
 *
 */

    function ckeditor_admin_main()
    {
        if(!xarSecurityCheck('AdminCKEditor')) return;

        if (xarModVars::get('modules', 'disableoverview') == 0) {
            xarResponse::Redirect(xarModURL('ckeditor', 'admin', 'overview'));
        } else {
            xarResponse::Redirect(xarModURL('ckeditor', 'admin', 'modifyconfig'));
        }
        // success
        return true;
    }
?>
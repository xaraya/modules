<?php
/**
 * Main admin GUI function, entry point
 *
 */

    function mailer_admin_main()
    {
        if(!xarSecurityCheck('ManageMailer')) return;

        if (xarModVars::get('modules', 'disableoverview') == 0) {
            return array();
        } else {
            xarResponse::redirect(xarModURL('mailer', 'admin', 'view'));
        }
        // success
        return true;
    }
?>
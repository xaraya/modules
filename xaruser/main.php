<?php
/**
 * Main user GUI function, entry point
 *
 */

    function xarayatesting_user_main()
    {
        // Security Check
        if (!xarSecurityCheck('ReadXarayatesting')) return;

        if (!xarModVars::get('modules', 'disableoverview') == 0) {
            xarResponse::Redirect(xarModURL('xarayatesting', 'user', 'testpage'));
        }
        return array();
    }

?>

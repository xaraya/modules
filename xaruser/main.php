<?php
/**
 * Main user GUI function, entry point
 *
 */

    function xarayatesting_user_main()
    {
        // Security Check
        if (!xarSecurityCheck('ReadXarayatesting')) return;

        if ((bool)xarModVars::get('modules', 'disableoverview') == true) {
            xarController::redirect(xarModURL('xarayatesting', 'user', 'testpage'));
        }
        return array();
    }

?>

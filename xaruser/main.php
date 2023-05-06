<?php
/**
 * Main user GUI function, entry point
 *
 */

    function xarayatesting_user_main()
    {
        // Security Check
        if (!xarSecurity::check('ReadXarayatesting')) return;

        if ((bool)xarModVars::get('modules', 'disableoverview') == true) {
            xarController::redirect(xarController::URL('xarayatesting', 'user', 'testpage'));
        }
        return array();
    }

?>

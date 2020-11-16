<?php
/**
 * Main admin GUI function, entry point
 *
 */

    function xarayatesting_admin_main()
    {
        if (!xarSecurityCheck('EditXarayatesting')) {
            return;
        }

        $refererinfo = xarController::$request->getInfo(xarServer::getVar('HTTP_REFERER'));
        $info = xarController::$request->getInfo();
        $samemodule = $info[0] == $refererinfo[0];

        if (((bool)xarModVars::get('modules', 'disableoverview') == false) || $samemodule) {
            return xarTplModule('xarayatesting', 'admin', 'overview');
        } else {
            xarController::redirect(xarModURL('xarayatesting', 'admin', 'view'));
            return true;
        }
    }

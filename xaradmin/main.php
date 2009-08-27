<?php
/**
 * Main admin GUI function, entry point
 *
 */

    function xarayatesting_admin_main()
    {
        if(!xarSecurityCheck('EditXarayatesting')) return;

        $refererinfo = xarRequest::getInfo(xarServer::getVar('HTTP_REFERER'));
        $info = xarRequest::getInfo();
        $samemodule = $info[0] == $refererinfo[0];

        if ((xarModVars::get('modules', 'disableoverview') == 0) || $samemodule){
            return xarTplModule('xarayatesting','admin','overview');
        } else {
            xarResponse::Redirect(xarModURL('xarayatesting', 'admin', 'view'));
            return true;
        }
    }
?>
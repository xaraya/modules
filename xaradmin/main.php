<?php

function query_admin_main()
{
    if(!xarSecurityCheck('AdminQuery')) return;

    $refererinfo = xarController::$request->getInfo(xarServer::getVar('HTTP_REFERER'));
    $info = xarController::$request->getInfo();
    $samemodule = $info[0] == $refererinfo[0];
    
    if ((xarModVars::get('modules', 'disableoverview') == 0) || $samemodule){
        return array();
    } else {
        xarController::redirect(xarModURL('query', 'admin', 'modifyconfig'));
    }
    // success
    return true;
}
?>
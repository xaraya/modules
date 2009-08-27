<?php

function query_admin_main()
{
    if(!xarSecurityCheck('AdminQuery')) return;

    $refererinfo = xarRequest::getInfo(xarServer::getVar('HTTP_REFERER'));
    $info = xarRequest::getInfo();
    $samemodule = $info[0] == $refererinfo[0];
    
    if ((xarModVars::get('modules', 'disableoverview') == 0) || $samemodule){
        return array();
    } else {
        xarResponse::Redirect(xarModURL('query', 'admin', 'modifyconfig'));
    }
    // success
    return true;
}
?>
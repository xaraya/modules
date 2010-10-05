<?php
function fulltext_admin_main()
{
    //if(!xarSecurityCheck('EditFulltext')) return;

    $refererinfo = xarController::$request->getInfo(xarServer::getVar('HTTP_REFERER'));
    $info = xarController::$request->getInfo();
    $samemodule = $info[0] == $refererinfo[0];

    if (((bool)xarModVars::get('modules', 'disableoverview') == false) || $samemodule){
        $data = array();
        if (!xarVarFetch('tab', 'pre:trim:lower:str:1:', $data['tab'], '', XARVAR_NOT_REQUIRED)) return;
        return xarTplModule('fulltext','admin','overview', $data);
    } else {
        xarController::redirect(xarModURL('fulltext', 'admin', 'modifyconfig'));
        return true;
    }
}
?>
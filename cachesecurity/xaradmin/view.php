<?php

/**
 * show the links for module items
 */
function cachesecurity_admin_view($args)
{ 
    if (!xarSecurityCheck('AdminCacheSecurity')) return;
    if (!xarVarFetch('error','str:0:',$error,'',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sync','str:1:',$sync,'',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('autosync','bool',$autosync,null,XARVAR_NOT_REQUIRED)) return;

    if ($autosync === false || $autosync === true) {
        xarConfigSetVar('CacheSecurity.AutoSync', $autosync);
    } else {
        $autosync = xarConfigGetVar('CacheSecurity.AutoSync');
    }
    
    if (!empty($sync)) {
        if (!xarSecConfirmAuthKey()) return;
        set_time_limit(300); //5 mins is enough?
        if (!xarModAPIFunc('cachesecurity','admin','sync'.$sync)) return;
    }

    $data = array();
    $data['syncinfo'] = xarModAPIFunc('cachesecurity','admin','getsyncinfo');
    $data['issynchronized'] = xarModAPIFunc('cachesecurity','admin','issynchronized');
    $data['ison'] = xarModAPIFunc('cachesecurity','admin','ison');
    $data['autosync'] = $autosync;
    $data['error'] = $error;
    $data['authid'] = xarSecGenAuthKey();
    

    return $data;
}

?>

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

    //Till the hooks are properly done i will leave autosync as false;
    xarConfigSetVar('CacheSecurity.AutoSync', false);
    /*
    if ($autosync === false || $autosync === true) {
        xarConfigSetVar('CacheSecurity.AutoSync', $autosync);
    } else {
        $autosync = xarConfigGetVar('CacheSecurity.AutoSync');
    }
    */
    
    if (!empty($sync)) {
        if (!xarSecConfirmAuthKey()) return;
        //This will make this run in the background even if the user
        //aborts it.
        ignore_user_abort(true);
        //Run it till necessary
        set_time_limit(0);
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

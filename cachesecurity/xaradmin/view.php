<?php

/**
 * show the links for module items
 */
function cachesecurity_admin_view($args)
{ 
    if (!xarSecurityCheck('AdminCacheSecurity')) return;
    if (!xarVarFetch('error','str:0:',$error,'',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sync','str:1:',$sync,'',XARVAR_NOT_REQUIRED)) return;

    if (!empty($sync)) {
        if (!xarModAPIFunc('cachesecurity','admin','sync'.$sync)) return;
    }

    $data = array();
    $data['syncinfo'] = xarModAPIFunc('cachesecurity','admin','getsyncinfo');
    $data['issynchronized'] = xarModAPIFunc('cachesecurity','admin','issynchronized');
    $data['ison'] = xarModAPIFunc('cachesecurity','admin','ison');
    $data['error'] = $error;

    return $data;
}

?>
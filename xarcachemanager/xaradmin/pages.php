<?php

/**
 * configure page caching (TODO)
 */
function xarcachemanager_admin_pages($args)
{ 
    extract($args);

    if (!xarSecurityCheck('AdminXarCache')) return;

    $data = array();

    $varCacheDir = xarCoreGetVarDirPath() . '/cache';
    if (file_exists($varCacheDir . '/output/cache.pagelevel')) {
        $iscached = 1;
    } else {
        $iscached = 0;
    }

    if (empty($iscached)) {
        $data['pages'] = array();
        return $data;
    }

    xarVarFetch('submit','str',$submit,'');
    if (!empty($submit)) {
        // Confirm authorisation code
        if (!xarSecConfirmAuthKey()) return;

        // ... do stuff ...
    }

    // Get some page caching configurations
    //$data['pages'] = xarModAPIfunc('xarcachemanager', 'admin', 'getpages');
    $data['pages'] = array('todo' => 'something ?');

    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

?>

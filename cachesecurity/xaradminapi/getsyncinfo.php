<?php

/**
 * Returns which parts are synchronized and which are not. 
 */
function cachesecurity_adminapi_getsyncinfo()
{
    $files = array();

    $synchronized['rolesgraph'] = xarConfigGetVar('CacheSecurity.rolesgraph');
    $synchronized['privsgraph'] = xarConfigGetVar('CacheSecurity.privsgraph');

    return $synchronized;
}

?>
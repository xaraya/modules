<?php

/*
 * Turn Security Caching on
 * @author Flavio Botelho <nuncanada@xaraya.com>
 */

function cachesecurity_adminapi_turnon()
{
    $issynchronized = xarModAPIFunc('cachesecurity','admin','issynchronized');

    if (!$issynchronized) {
        $msg = xarML('Trying to turn security caching on without it being synchronized! Please, you need to get the cache synchronized first.');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'FUNCTION_FAILED', new SystemException($msg));
        return;
    }

    if (!xarConfigSetVar('CacheSecurity.on', true)) return false;
  
     return true;
}

?>
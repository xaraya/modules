<?php

/*
 * Turn Security Caching on
 * @author Flavio Botelho <nuncanada@xaraya.com>
 */

function cachesecurity_adminapi_turnon()
{
    $filename = xarModAPIFunc('cachesecurity','admin','filename', array('part'=>'on'));
    $issynchronized = xarModAPIFunc('cachesecurity','admin','issynchronized');

    if (!$issynchronized) {
        $msg = xarML('Trying to turn security caching on without it being synchronized! Please, you need to get the cache synchronized first.');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'FUNCTION_FAILED', new SystemException($msg));
        return;
    }

    if (!touch($filename)) return false;
  
     return true;
}

?>
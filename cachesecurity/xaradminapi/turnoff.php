<?php

/**
 * Turn security caching system off 
 */
function cachesecurity_adminapi_turnoff ()
{
    if (!xarConfigSetVar('CacheSecurity.on', false)) return false;
  
    return true;
}

?>
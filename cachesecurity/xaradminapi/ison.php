<?php

/**
 * Is security caching currently on? 
 */
function cachesecurity_adminapi_ison()
{
    return xarConfigGetVar('CacheSecurity.on');
}

?>
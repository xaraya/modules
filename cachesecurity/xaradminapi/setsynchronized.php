<?php

/**
 * Sets the touch file that means the given system part is synchronized 
 */
function cachesecurity_adminapi_setsynchronized($args)
{
    xarConfigSetVar('CacheSecurity.'.$args['part'], true);

    return true;
}

?>
<?php

/**
 * Unsets the touch file which means the given system part is not synchronized 
 */
function cachesecurity_adminapi_unsetsynchronized($args)
{
    xarConfigSetVar('CacheSecurity.'.$args['part'], false);

    return true;
}

?>
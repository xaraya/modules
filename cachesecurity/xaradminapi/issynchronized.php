<?php

/**
 * Is security caching currently on? 
 */
function cachesecurity_adminapi_issynchronized()
{
    $parts = xarModAPIFunc('cachesecurity','admin','getsyncinfo');

    $synchronized = true;
    foreach ($parts as $boolean) {
        if ($boolean == false) {
            $synchronized = false;
            break;
        }
    }

    return $synchronized;
}

?>
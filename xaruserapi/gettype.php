<?php

// Get a single page type.

function xarpages_userapi_gettype($args)
{
    $types = xarMod::apiFunc('xarpages', 'user', 'gettypes', $args);

    if (empty($types) || count($types) > 1) {
        return;
    }

    return(reset($types));
}

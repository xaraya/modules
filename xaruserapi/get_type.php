<?php

// Get a single page type.

function xarpages_userapi_get_type($args)
{
    $types = xarMod::apiFunc('xarpages', 'user', 'get_types', $args);

    if (empty($types) || count($types) > 1) {
        return;
    }

    return(reset($types));
}

?>
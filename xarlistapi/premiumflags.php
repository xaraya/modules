<?php

/**
 * List of premium flags (keys and values).
 */

function mag_listapi_premiumflags($args)
{
    extract($args);

    // Get module parameters
    extract(xarModAPIfunc('mag', 'user', 'params',
        array(
            'knames' => 'premium_flags'
        )
    ));

    $return = $premium_flags;

    return $return;
}

?>
<?php

/**
 * List of premium flags for issues, including the 'inherit' option (keys and values).
 */

function mag_listapi_issuepremiumflags($args)
{
    extract($args);

    $return = xarModAPIfunc('mag', 'list', 'premiumflags');
    $return = array_merge(array('' => xarML('Inherit from magazine')), $return);

    return $return;
}

?>
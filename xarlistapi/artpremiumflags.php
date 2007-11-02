<?php

/**
 * List of premium flags for articles, including the 'inherit' option (keys and values).
 */

function mag_listapi_artpremiumflags($args)
{
    extract($args);

    $return = xarModAPIfunc('mag', 'list', 'premiumflags');
    $return = array_merge(array('' => xarML('Inherit from issue')), $return);

    return $return;
}

?>
<?php

/**
 * Overview of available magazines, or a single magazine.
 *
 * @param mid integer Magazine ID
 * @param mag string Magazine reference
 *
 */

function mag_user_main($args)
{
    // Default view.

    return xarModFunc('mag', 'user', 'mags');
}

?>
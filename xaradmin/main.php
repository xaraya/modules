<?php

/**
 * the main administration function
 */
function pubsub_admin_main()
{
    // Security check
    if (!xarSecurityCheck('AdminPubSub')) return;

    // Return the template variables defined in this function
    return array();
}

?>

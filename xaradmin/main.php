<?php

/**
 * the main administration function
 */
function pubsub_admin_main()
{
    // Security check
    if (!xarSecurityCheck('AdminPubSub')) return;
        xarResponseRedirect(xarModURL('pubsub', 'admin', 'viewall'));
    // Return the template variables defined in this function
    return array();
}

?>

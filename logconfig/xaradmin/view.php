<?php

/**
 * view items
 */
function logconfig_admin_view()
{
    $data = xarModAPIFunc('logconfig','admin','menu');

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AdminLogConfig')) return;

    $data['itemsnum'] = xarModGetVar('logconfig','itemstypenumber');

    // Return the template variables defined in this function
    return $data;
}

?>
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

    if (!xarModAPIFunc('logconfig','admin','islogon')
        && xarLogFallbackPossible()) 
    {
        $data['fallbackOn'] = true;
    } else {
        $data['fallbackOn'] = false;
    }
    
    $data['fallbackFile'] = xarLogFallbackFile();

    // Return the template variables defined in this function
    return $data;
}

?>
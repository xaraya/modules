<?php

/**
 * the main administration function
 */
function logconfig_admin_main()
{
    if (!xarSecurityCheck('AdminLogConfig')) return;

    if (!(xarModGetVar('adminpanels', 'overview') == 0)){
        xarResponseRedirect(xarModURL('logconfig', 'admin', 'view'));
    }

    $data = xarModAPIFunc('logconfig','admin','menu');

    // Return the template variables defined in this function
    return $data;
}

?>
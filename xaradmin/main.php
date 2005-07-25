<?php

/**
 * the main administration function
 */
function uploads_admin_main()
{
    // Security Check
    if (!xarSecurityCheck('EditUploads')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0) {
        return array();
    } else {
        xarResponseRedirect(xarModURL('uploads', 'admin', 'view'));
    }
    // success
    return true;
}

?>
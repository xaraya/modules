<?php

/**
 * the main administration function
 */
function images_admin_main()
{
    // Security Check
    if (!xarSecurityCheck('AdminImages')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0) {
        return array();
    } else {
        xarResponseRedirect(xarModURL('images', 'admin', 'modifyconfig'));
    }
    // success
    return true;
}

?>

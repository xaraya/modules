<?php

/**
 * the main administration function
 */
function filemanager_admin_main()
{
    // Security Check
    if (!xarSecurityCheck('EditFileManager')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0) {
        return array();
    } else {
        xarResponseRedirect(xarModURL('filemanager', 'admin', 'view'));
    }
    // success
    return true;
}

?>
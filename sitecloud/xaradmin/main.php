<?php
function sitecloud_admin_main()
{
    // Security Check
	if(!xarSecurityCheck('Editsitecloud')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0){
        // Return the output
        return array();
    } else {
        xarResponseRedirect(xarModURL('sitecloud', 'admin', 'view'));
    }
    // success
    return true;
}
?>

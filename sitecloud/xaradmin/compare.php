<?php
function sitecloud_admin_compare()
{
	if(!xarSecurityCheck('Adminsitecloud')) return;
    // The API function is called
    if (!xarModAPIFunc('sitecloud',
                       'scheduler',
                       'compare')) return;
    xarResponseRedirect(xarModURL('sitecloud', 'admin', 'view'));
    // Return
    return true;
}
?>

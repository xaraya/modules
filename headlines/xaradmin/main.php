<?php
function headlines_admin_main()
{
    // Security Check
	if(!xarSecurityCheck('EditHeadlines')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0){
        // Return the output
        return array();
    } else {
        xarResponseRedirect(xarModURL('headlines', 'admin', 'view'));
    }
    // success
    return true;
}
?>

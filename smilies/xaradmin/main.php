<?php
/**
 * Add a standard screen upon entry to the module.
 * @returns output
 * @return output with smilies Menu information
 */
function smilies_admin_main()
{
    // Security Check
	if(!xarSecurityCheck('EditSmilies')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0){
        // Return the output
        return array();
    } else {
        xarResponseRedirect(xarModURL('smilies', 'admin', 'view'));
    }
    // success
    return true;
}
?>
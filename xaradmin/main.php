<?php
/**
 * Overview Menu
 */
function comments_admin_main()
{
    if(!xarSecurityCheck('Comments-Admin')){
        return;
    }
    // we only really need to show the default view (overview in this case)
    if (xarModGetVar('adminpanels', 'overview') == 0){
        xarResponseRedirect(xarModURL('comments', 'admin', 'view'));
    } else {
        xarResponseRedirect(xarModURL('comments', 'admin', 'stats'));
    }
    // success
    return true;
}
?>
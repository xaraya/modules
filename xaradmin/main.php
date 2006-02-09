<?php

function owner_admin_main($args)
{
    // Security Check (this the right one?)
    if(!xarSecurityCheck('ChangeOwner')) return;
    $data = array();
    if (!xarModGetVar('adminpanels', 'overview')) {
        // Normal overview page
        return $data;
    } else {
        // If args specified allow functionless addressing the changeowner function
        xarResponseRedirect(xarModURL('owner', 'admin', 'changeowner',$args));
        return true;
    }
    return $data;
}
?>

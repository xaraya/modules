<?php

function polls_admin_main()
{
    if(!xarSecurityCheck('AdminPolls')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0){
        // Return the output
        return array();
    } else {
        xarResponseRedirect(xarModURL('polls', 'admin', 'list'));
    }
    // success
}

?>
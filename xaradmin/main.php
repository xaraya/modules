<?php

function googlesearch_admin_main()
{
    // Security Check
  if(!xarSecurityCheck('Admingooglesearch')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0){
        // Return the output
        return array();
    } else {
        xarResponseRedirect(xarModURL('googlesearch', 'admin', 'modifyconfig'));
    }
    // success
    return true;
}
?>
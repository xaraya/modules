<?php

/**
 * the main administration function
 */
function categories_admin_main()
{

    // Security check
    if(!xarSecurityCheck('ViewCategories')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0){
        // Return the output
        return array();
    } else {
        xarResponseRedirect(xarModURL('categories', 'admin', 'viewcats'));
    }

    return true;
}

?>
<?php

/**
 * the main administration function
 */
function articles_admin_main()
{

// Security Check
    if (!xarSecurityCheck('EditArticles')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0){
        $welcome = '';

        // Return the template variables defined in this function
        return array('welcome' => $welcome);
    } else {
        xarResponseRedirect(xarModURL('articles', 'admin', 'view'));
    }
    // success
    return true;

}

?>
<?php

/**
 * the main administration function
 */
function julian_admin_main()
{

// Security Check
    if (!xarSecurityCheck('EditJulian')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0){
        $welcome = '';

        // Return the template variables defined in this function
        return array('welcome' => $welcome);
    } else {
        xarResponseRedirect(xarModURL('julian', 'admin', 'modifyconfig'));
    }
    // success
    return true;

}

?>
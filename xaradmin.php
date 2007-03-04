<?php
xarModAPILoad('calendar','defines');

/**
 * Add a standard screen upon entry to the module.
 * @returns output
 * @return true
 */
function calendar_admin_main()
{
    // Security Check
    if (!xarSecurityCheck('AdminCalendar')) {
        return;
    }

    if (xarModGetVar('adminpanels', 'overview') == 0) {
        // Return the output
        return array();
    } else {
        xarResponseRedirect(xarModURL('calendar', 'admin', 'modifyconfig'));
    }
    // success
    return true;
}

?>
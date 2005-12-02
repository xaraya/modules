<?php

/**
 * the main administration function
 *
 * @author Curtis Farnham
 * @access public
 * @param no $ parameters
 * @return true on success or void on failure
 * @throws XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION'
 */
function bible_admin_main()
{
    // Security Check
    if (!xarSecurityCheck('AdminBible')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0) {
        // Return the output
        return xarModAPIFunc('bible', 'admin', 'menu');
    } else {
        xarResponseRedirect(xarModURL('bible', 'admin', 'view'));
    }
    // success
    return true;
}

?>

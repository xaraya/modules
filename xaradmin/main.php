<?php

/**
 * Main admin gui function, entry point
 *
 * @return bool
 */
function newsgroups_admin_main()
{
// Security Check
    if(!xarSecurityCheck('AdminNewsgroups')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0){
        // Return the output
        return array();
    } else {
        xarResponseRedirect(xarModURL('newsgroups', 'admin', 'modifyconfig'));
    }
    // success
    return true;
}

?>
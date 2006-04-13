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

        xarResponseRedirect(xarModURL('newsgroups', 'admin', 'modifyconfig'));
    // success
    return true;
}

?>
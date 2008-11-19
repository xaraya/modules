<?php
/**
 * Main user GUI function, entry point
 *
 */

    function karma_user_main()
    {
        // Security Check
        if (!xarSecurityCheck('ReadKarma')) return;

//        xarResponseRedirect(xarModURL('karma', 'user', 'view'));
        // success
        return array(); //true;
    }

?>

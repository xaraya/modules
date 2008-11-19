<?php
/**
 * Main user GUI function, entry point
 *
 */

    function foo_user_main()
    {
        // Security Check
        if (!xarSecurityCheck('ReadFoo')) return;

//        xarResponseRedirect(xarModURL('foo', 'user', 'view'));
        // success
        return array(); //true;
    }

?>

<?php
/**
 * Main user GUI function, entry point
 *
 */

    function mailer_user_main()
    {
        // Security Check
        if (!xarSecurityCheck('ReadMailer')) return;

//        xarResponseRedirect(xarModURL('mailer', 'user', 'view'));
        // success
        return array(); //true;
    }

?>

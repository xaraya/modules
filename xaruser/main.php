<?php
/**
 * Main user GUI function, entry point
 *
 */

    function ckeditor_user_main()
    {
        // Security Check
        if (!xarSecurityCheck('ReadCKEditor')) return;

//        xarResponseRedirect(xarModURL('ckeditor', 'user', 'view'));
        // success
        return array(); //true;
    }

?>

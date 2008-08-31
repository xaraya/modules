<?php
/**
 * Display the Site Tests
 *
 */
    function xarayatesting_admin_sitetests($args)
    {
        xarResponseRedirect(xarModURL('xarayatesting','user','view',$args));
        return true;
    }
?>
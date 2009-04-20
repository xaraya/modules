<?php
/**
 * Display the Site Tests
 *
 */
    function xarayatesting_admin_sitetests($args)
    {
        xarResponse::Redirect(xarModURL('xarayatesting','user','view',$args));
        return true;
    }
?>
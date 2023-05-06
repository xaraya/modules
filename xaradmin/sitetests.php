<?php
/**
 * Display the Site Tests
 *
 */
    function xarayatesting_admin_sitetests($args)
    {
        xarController::redirect(xarController::URL('xarayatesting','user','view',$args));
        return true;
    }
?>
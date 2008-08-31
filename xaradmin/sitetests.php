<?php
/**
 * Display the Site Tests
 *
 */
    function xarayatesting_admin_sitetests($args)
    {
        return xarModFunc('xarayatesting','user','view',$args);
    }
?>
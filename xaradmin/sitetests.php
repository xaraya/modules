<?php
/**
 * Display the Site Tests
 *
 */
    function xarayatesting_admin_sitetests($args)
    {
        xarController::redirect(xarModURL('xarayatesting', 'user', 'view', $args));
        return true;
    }

<?php
/**
 * Run the Xaraya Scan Tests
 *
 */
    function xarayatesting_admin_othertests($args)
    {
        xarController::redirect(xarModURL('xarayatesting','user','othertests',$args));
        return true;
    }
?>
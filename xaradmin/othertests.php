<?php
/**
 * Run the Xaraya Scan Tests
 *
 */
    function xarayatesting_admin_othertests($args)
    {
        xarResponse::Redirect(xarModURL('xarayatesting','user','othertests',$args));
        return true;
    }
?>
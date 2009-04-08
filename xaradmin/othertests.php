<?php
/**
 * Run the Xaraya Scan Tests
 *
 */
    function xarayatesting_admin_othertests($args)
    {
        xarResponseRedirect(xarModURL('xarayatesting','user','othertests',$args));
        return true;
    }
?>
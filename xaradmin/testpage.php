<?php
/**
 * Run the Xaraya Unit Tests
 *
 */
    function xarayatesting_admin_testpage($args)
    {
        xarResponseRedirect(xarModURL('xarayatesting','user','testpage',$args));
        return true;
    }
?>
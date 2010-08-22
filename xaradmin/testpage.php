<?php
/**
 * Run the Xaraya Unit Tests
 *
 */
    function xarayatesting_admin_testpage($args)
    {
        xarController::redirect(xarModURL('xarayatesting','user','testpage',$args));
        return true;
    }
?>
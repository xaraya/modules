<?php
/**
 * Run the Xaraya Unit Tests
 *
 */
    function xarayatesting_admin_testpage($args)
    {
        return xarModFunc('xarayatesting','user','testpage',$args);
    }
?>
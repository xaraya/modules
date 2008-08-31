<?php
/**
 * Return the options for the admin menu
 *
 */

    function xarayatesting_adminapi_getmenulinks()
    {
        return xarModAPIFunc('base','admin','menuarray',array('module' => 'xarayatesting'));
    }

?>
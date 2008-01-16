<?php
/**
 * Return the options for the admin menu
 *
 */

    function foo_adminapi_getmenulinks()
    {
        return xarModAPIFunc('base','admin','menuarray',array('module' => 'foo'));
    }

?>
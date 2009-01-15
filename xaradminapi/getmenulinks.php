<?php
/**
 * Return the options for the admin menu
 *
 */

    function mailer_adminapi_getmenulinks()
    {
        return xarModAPIFunc('base','admin','menuarray',array('module' => 'mailer'));
    }

?>
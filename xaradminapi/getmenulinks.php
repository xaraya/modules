<?php
/**
 * Return the options for the admin menu
 *
 */

    function ckeditor_adminapi_getmenulinks()
    {
        return xarModAPIFunc('base','admin','menuarray',array('module' => 'ckeditor'));
    }

?>
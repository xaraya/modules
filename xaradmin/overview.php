<?php
/**
 * Main admin GUI function, entry point
 *
 */

    function ckeditor_admin_overview()
    {
        if(!xarSecurityCheck('ReadCKEditor')) return;
 
        // success
        return xarTplModule('ckeditor','admin','overview');  
    }
?>
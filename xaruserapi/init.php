<?php

/**
 *  Sets up overlib
 */

function overlib_userapi_init()
{
    // only dump the javascript on the first call
    // otherwise just exit
    if(!defined('XAR_OVERLIB_INIT')) { 
        define('XAR_OVERLIB_INIT',true); 
        return xarTplFile('modules/overlib/xartemplates/userapi-init.xd',array());
    }

}
?>

<?php

function overlib_init()
{
    // register the blocklayout tags
    xarTplRegisterTag('overlib','overlib-init',array(),'overlib_userapi_bl_init');
    xarTplRegisterTag('overlib','overlib-popup-open',array(),'overlib_userapi_bl_open');
    xarTplRegisterTag('overlib','overlib-popup-close',array(),'overlib_userapi_bl_close');
    return true;
}

function overlib_upgrade()
{
    return true;
}

function overlib_delete()
{
    // un-register the blocklayout tags
    xarTplUnregisterTag('overlib-init');
    xarTplUnregisterTag('overlib-popup-open');
    xarTplUnregisterTag('overlib-popup-close');
    return true;
}
?>

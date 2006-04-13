<?php
/**
 * Wrapper for setting module hook settings.
 */
function security_adminapi_set_hook_settings($args)
{
    extract($args);

    if( empty($modid) ){ return false; }
    if( empty($settings) ){ return false; }

    $var_name = "settings.$modid";
    if( !empty($itemtype) ){ $var_name .= ".$itemtype"; }

    return xarModSetVar('security', $var_name, serialize($settings));
}
?>
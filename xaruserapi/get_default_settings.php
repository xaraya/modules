<?php

function security_userapi_get_default_settings($args)
{
    extract($args);

    $vars = array("settings");
    if( !empty($modid) )
    {
        array_push($vars, "settings.$modid");
        if( !empty($itemtype) )
        {
            array_push($vars, "settings.$modid.$itemtype");
        }
    }
    
    $settings = array();
    while( empty($settings) && ($var = array_pop($vars)) != null )
    {
        $settings =@ unserialize(xarModGetVar('security', $var));
    }
    
    if( empty($settings) )
    {
        $settings = array(
            'exclude_groups' => array(),
            'levels' => array(
                'user' => SECURITY_OVERVIEW+SECURITY_READ+SECURITY_COMMENT+SECURITY_WRITE+SECURITY_ADMIN,
                'groups' => array(),
                'world' => SECURITY_OVERVIEW+SECURITY_READ
            )
        );
    }
    
    return $settings;
}
?>
<?php

/**
 * Save configuration settings in the config file and modVars
 * 
 * @author jsb <jsb@xaraya.com>
 * @access public 
 * @param array $cachingConfiguration cachingConfiguration to be prep for a template
 * @returns array_keys
 * @return array of cachingConfiguration with '.' removed from keys or void
 */
function xarcachemanager_adminapi_config_tpl_prep($cachingConfiguration)
{
    if(empty($cachingConfiguration) || !is_array($cachingConfiguration)) {
        return;
    }
    
    $keyslist = str_replace( '.', '', array_keys($cachingConfiguration));
    $valueslist = array_values($cachingConfiguration);
    $settings = array();

    $arraysize = sizeof($keyslist);
    for ($i=0;$i<$arraysize;$i++) {
        $settings[$keyslist[$i]] = $valueslist[$i];
    }

    return $settings;
}

?>

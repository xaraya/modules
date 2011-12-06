<?php
function keywords_hooksapi_getsettings(Array $args=array())
{
    extract($args);

    if (!empty($module_id))
        $module = xarMod::getName($module_id);
    if (empty($module))
        $module = xarMod::getName();
    
    if (empty($itemtype))
        $itemtype = 0;
    
    sys::import('modules.keywords.class.main');
    // keywords module config requested or per module config is disabled, return defaults 
    if ($module == 'keywords' || !empty(Keywords::getConfig('keywords')->global_config))
        return Keywords::getConfig('keywords', 0, array('config_state' => 'default'))->getPublicProperties();

    // if we're here, per module config is enabled, and this isn't the keywords module
    // if module defaults requested or per itemtype config is disabled, return module defaults
    if (empty($itemtype) || !empty(Keywords::getConfig($module, 0)->global_config))
        return Keywords::getConfig($module, 0, array('config_state' => 'module'))->getPublicProperties();
    
    // if we're here, per itemtype config is enabled and this isn't itemtype 0 
    return Keywords::getConfig($module, $itemtype, array('config_state' => 'itemtype'))->getPublicProperties();

}
?>
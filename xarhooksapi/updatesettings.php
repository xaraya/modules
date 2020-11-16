<?php
function keywords_hooksapi_updatesettings(array $args=array())
{
    extract($args);

    if (isset($module_id)) {
        $module = xarMod::getName($module_id);
    }
    if (!isset($module)) {
        $module = xarMod::getName();
    }

    if (empty($itemtype)) {
        $itemtype = 0;
    }

    $defaults = xarMod::apiFunc(
        'keywords',
        'hooks',
        'getsettings',
        array(
            'module' => $module,
            'itemtype' => $itemtype,
        )
    );
    
    if ($defaults['config_state'] == 'default') {
        // per module settings disabled, if this isn't the keywords module, bail
        if ($module != 'keywords') {
            return;
        }
    } elseif ($defaults['config_state'] == 'module') {
        // per itemtype settings disabled, if this isn't itemtype 0, bail
        if (!empty($itemtype)) {
            return;
        }
    }

    if (empty($settings)) {
        $settings = $defaults;
    }
        
    Keywords::setConfig($module, $itemtype, $settings);

    return true;
}

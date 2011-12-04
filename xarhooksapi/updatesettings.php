<?php
function keywords_hooksapi_updatesettings(Array $args=array())
{
    extract($args);

    if (isset($module_id))
        $module = xarMod::getName($module_id);
    if (!isset($module))
        $module = xarMod::getName();

    if (empty($itemtype))
        $itemtype = 0;

    $defaults = xarMod::apiFunc('keywords', 'hooks', 'getsettings',
        array(
            'module' => $module,
            'itemtype' => $itemtype,
        ));

    if (!empty($defaults['default_config'])) {
        // per module settings disabled, if this isn't the keywords module, bail
        if ($module != 'keywords') return;
    } elseif (!empty($defaults['module_config'])) {
        // per itemtype settings disabled, if this isn't itemtype 0, bail
        if (!empty($itemtype)) return;
    }

    if (empty($settings))
        $settings = $defaults;

    foreach (array_keys($settings) as $key)
        if (!array_key_exists($key, $defaults)) unset($settings[$key]);
    foreach (array_keys($defaults) as $key)
        if (!array_key_exists($key, $settings)) $settings[$key] = $default[$key];

    if (!empty($defaults['default_config']) || !empty($defaults['module_config'])) {
        $modvar = 'keywords_config';
    } else {
        $modvar = 'keywords_config_'.$itemtype;
    }
    xarModVars::set($module, $modvar, serialize($settings));

    return true;
}
?>
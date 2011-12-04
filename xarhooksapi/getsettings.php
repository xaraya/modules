<?php
function keywords_hooksapi_getsettings(Array $args=array())
{
    $config = array(
        'global_config' => false,    // apply defaults to all modules (if keywords) and/or itemtypes
        'restrict_words' => false,   // use a restricted list of keywords
        'index_id' => xarMod::apiFunc('keywords', 'index', 'getid', array('module' => 'keywords')),
    );

    extract($args);

    if (!empty($module_id))
        $module = xarMod::getName($module_id);
    if (empty($module))
        list($module) = xarController::$request->getInfo();

    // get the default config for the keywords module
    $keywords_config = @unserialize(xarModVars::get('keywords', 'keywords_config'));

    if (!empty($keywords_config))
        $config = $keywords_config;
    $config['index_id'] = xarMod::apiFunc('keywords', 'index', 'getid',
        array('module' => 'keywords'));
    // first run, (or unset manually elsewhere), use the defaults
    if (empty($keywords_config))
        // while we're here, set them
        xarModVars::set('keywords', 'keywords_config', serialize($config));

    // see if per module config is allowed, and this isn't keywords module (default) config
    if (empty($config['global_config']) && $module != 'keywords') {
        // per module config is allowed, not the keywords module
        // get the default config for the requested module
        $module_config = @unserialize(xarModVars::get($module, 'keywords_config'));

        if (!empty($module_config))
            $config = $module_config;
        $config['index_id'] = xarMod::apiFunc('keywords', 'index', 'getid',
            array('module' => $module));
        // first run, (or unset manually elsewhere)
        if (empty($module_config))
            // while we're here, set them
            xarModVars::set($module, 'keywords_config', serialize($config));
        // see if per itemtype config is allowed and we have an itemtype
        if (empty($config['global_config']) && !empty($itemtype)) {
            // seems per itemtype config is allowed and we have an itemtype
            // get the config for the requested module itemtype
            $itemtype_config = @unserialize(xarModVars::get($module, 'keywords_config_'.$itemtype));

            if (!empty($itemtype_config))
                $config = $itemtype_config;
            $config['index_id'] = xarMod::apiFunc('keywords', 'index', 'getid',
                array('module' => $module, 'itemtype' => $itemtype));
            // first run, (or unset manually elsewhere), use the module defaults
            if (empty($itemtype_config))
                // while we're here, set them
                xarModVars::set($module, 'keywords_config_'.$itemtype, serialize($config));
            $config['itemtype_config'] = $itemtype;
            unset($config['module_config'], $config['default_config']);
        } else {
            $config['module_config'] = $module;
            unset($config['default_config'], $config['itemtype_config']);
        }
    } else {
        $config['default_config'] = $module;
        unset($config['module_config'], $config['itemtype_config']);
    }

    return $config;

}
?>
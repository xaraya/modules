<?php
/**
 * Get configuration of variable caching for variables
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.xarcachemanager.class.manager');

/**
 * get configuration of variable caching for all variables
 *
 * @return array variable caching configurations
 */
function xarcachemanager_adminapi_getvariables($args)
{
    extract($args);

    // Get all variable cache settings
    $variablesettings = [];
    $serialsettings = xarModVars::get('dynamicdata', 'variablecache_settings');
    if (!empty($serialsettings)) {
        $variablesettings = unserialize($serialsettings);
    }

    // Get all variables
    //$variables = xarMod::apiFunc('dynamicdata', 'user', 'getvariables');
    $variables = array_keys(xarVariableCache::getCacheSettings());

    $variableconfig = [];
    foreach ($variables as $name) {
        $settings = [];
        $settings['name'] = $name;
        if (isset($variablesettings[$name])) {
            $settings = $variablesettings[$name];
            if ($settings['cacheexpire'] > 0) {
                $settings['cacheexpire'] = xarMod::apiFunc(
                    'xarcachemanager',
                    'admin',
                    'convertseconds',
                    ['starttime' => $settings['cacheexpire'],
                                                                 'direction' => 'from', ]
                );
            } else {
                $settings['cacheexpire'] = '';
            }
        } else {
            $settings['name'] = $name;
            // flip from docache in config to nocache in settings
            $settings['nocache'] = 1;
            $settings['cacheexpire'] = '';
        }
        $variableconfig[$name] = $settings;
    }
    return $variableconfig;
}

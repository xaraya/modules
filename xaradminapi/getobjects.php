<?php
/**
 * Get configuration of object caching for objects
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
 * get configuration of object caching for all objects
 *
 * @return array object caching configurations
 */
function xarcachemanager_adminapi_getobjects($args)
{
    extract($args);

    // Get all object cache settings
    $objectsettings = [];
    $serialsettings = xarModVars::get('dynamicdata', 'objectcache_settings');
    if (!empty($serialsettings)) {
        $objectsettings = unserialize($serialsettings);
    }

    // Get all objects
    $objects = xarMod::apiFunc('dynamicdata', 'user', 'getobjects');

    // Get default object methods to cache
    $defaultobjectmethods = unserialize(xarModVars::get('xarcachemanager', 'DefaultObjectCacheMethods'));

    // CHECKME: do we want to support settings for non-objects (like tables) ?

    $objectconfig = [];
    foreach (array_keys($objects) as $id) {
        // TODO: filter on visibility, dummy datastores etc. ?
        if ($objects[$id]['objectid'] < 4 ||
            $objects[$id]['moduleid'] == xarMod::getRegId('roles') ||
            $objects[$id]['moduleid'] == xarMod::getRegId('privileges')) {
            continue;
        }
        // use the object name as key for easy lookup in xarObjectCache
        $name = $objects[$id]['name'];
        $objectconfig[$name] = $objects[$id];
        $objectconfig[$name]['cachesettings'] = [];
        if (isset($objectsettings[$name])) {
            foreach ($objectsettings[$name] as $method => $settings) {
                if ($settings['cacheexpire'] > 0) {
                    $settings['cacheexpire'] = xarMod::apiFunc(
                        'xarcachemanager',
                        'admin',
                        'convertseconds',
                        ['starttime' => $settings['cacheexpire'],
                                                                     'direction' => 'from', ]
                    );
                }
                $objectconfig[$name]['cachesettings'][$method] = $settings;
            }
        }
        // TODO: Try loading some defaults from the object config ?
        foreach ($defaultobjectmethods as $method => $docache) {
            if (isset($objectconfig[$name]['cachesettings'][$method])) {
                continue;
            }
            $settings = [];
            // flip from docache in config to nocache in settings
            if (!empty($docache)) {
                $settings['nocache'] = 0;
            } else {
                $settings['nocache'] = 1;
            }
            $settings['usershared'] = 1;
            $settings['cacheexpire'] = '';
            $objectconfig[$name]['cachesettings'][$method] = $settings;
        }
    }
    return $objectconfig;
}

<?php
/**
 * Config object caching
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.xarcachemanager.class.cache_manager');

/**
 * configure object caching
 * @return array
 */
function xarcachemanager_admin_objects($args)
{
    extract($args);

    if (!xarSecurity::check('AdminXarCache')) {
        return;
    }

    $data = [];
    if (!xarCache::$outputCacheIsEnabled || !xarOutputCache::$objectCacheIsEnabled) {
        $data['objects'] = [];
        return $data;
    }

    xarVar::fetch('submit', 'str', $submit, '');
    if (!empty($submit)) {
        // Confirm authorisation code
        if (!xarSec::confirmAuthKey()) {
            return;
        }

        xarVar::fetch('docache', 'isset', $docache, []);
        xarVar::fetch('usershared', 'isset', $usershared, []);
        xarVar::fetch('cacheexpire', 'isset', $cacheexpire, []);

        $newobjects = [];
        // loop over something that should return values for every object
        foreach ($cacheexpire as $name => $expirelist) {
            $newobjects[$name] = [];
            foreach ($expirelist as $method => $expire) {
                $newobjects[$name][$method] = [];
                // flip from docache in template to nocache in settings
                if (!empty($docache[$name]) && !empty($docache[$name][$method])) {
                    $newobjects[$name][$method]['nocache'] = 0;
                } else {
                    $newobjects[$name][$method]['nocache'] = 1;
                }
                if (!empty($usershared[$name]) && !empty($usershared[$name][$method])) {
                    $newobjects[$name][$method]['usershared'] = intval($usershared[$name][$method]);
                } else {
                    $newobjects[$name][$method]['usershared'] = 0;
                }
                if (!empty($expire)) {
                    $expire = xarCache_Manager::convertseconds(
                        ['starttime' => $expire,
                                                    'direction' => 'to', ]
                    );
                } elseif ($expire === '0') {
                    $expire = 0;
                } else {
                    $expire = null;
                }
                $newobjects[$name][$method]['cacheexpire'] = $expire;
            }
        }
        // save settings to dynamicdata in case xarcachemanager is removed later
        xarModVars::set('dynamicdata', 'objectcache_settings', serialize($newobjects));

        // objects could be anywhere, we're not smart enough not know exactly where yet
        $key = '';
        // so just flush all pages
        if (xarOutputCache::$pageCacheIsEnabled) {
            xarPageCache::flushCached($key);
        }
        // and flush the objects
        xarObjectCache::flushCached($key);
        if (xarModVars::get('xarcachemanager', 'AutoRegenSessionless')) {
            xarMod::apiFunc('xarcachemanager', 'admin', 'regenstatic');
        }
    }

    // Get all object caching configurations
    $data['objects'] = xarMod::apiFunc('xarcachemanager', 'admin', 'getobjects');

    $data['authid'] = xarSec::genAuthKey();
    return $data;
}

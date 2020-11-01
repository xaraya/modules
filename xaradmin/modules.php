<?php
/**
 * Config module caching
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
 * configure module caching
 * @return array
 */
function xarcachemanager_admin_modules($args)
{
    extract($args);

    if (!xarSecurity::check('AdminXarCache')) {
        return;
    }

    $data = array();
    if (!xarCache::$outputCacheIsEnabled || !xarOutputCache::$moduleCacheIsEnabled) {
        $data['modules'] = array();
        return $data;
    }

    xarVar::fetch('submit', 'str', $submit, '');
    if (!empty($submit)) {
        // Confirm authorisation code
        if (!xarSec::confirmAuthKey()) {
            return;
        }

        xarVar::fetch('docache', 'isset', $docache, array());
        xarVar::fetch('usershared', 'isset', $usershared, array());
        xarVar::fetch('params', 'isset', $params, array());
        xarVar::fetch('cacheexpire', 'isset', $cacheexpire, array());

        $newmodules = array();
        // loop over something that should return values for every module
        foreach ($cacheexpire as $name => $expirelist) {
            $newmodules[$name] = array();
            foreach ($expirelist as $func => $expire) {
                $newmodules[$name][$func] = array();
                // flip from docache in template to nocache in settings
                if (!empty($docache[$name]) && !empty($docache[$name][$func])) {
                    $newmodules[$name][$func]['nocache'] = 0;
                } else {
                    $newmodules[$name][$func]['nocache'] = 1;
                }
                if (!empty($usershared[$name]) && !empty($usershared[$name][$func])) {
                    $newmodules[$name][$func]['usershared'] = intval($usershared[$name][$func]);
                } else {
                    $newmodules[$name][$func]['usershared'] = 0;
                }
                if (!empty($params[$name]) && !empty($params[$name][$func])) {
                    $newmodules[$name][$func]['params'] = $params[$name][$func];
                } else {
                    $newmodules[$name][$func]['params'] = '';
                }
                if (!empty($expire)) {
                    $expire = xarCache_Manager::convertseconds(
                        array('starttime' => $expire,
                                                    'direction' => 'to')
                    );
                } elseif ($expire === '0') {
                    $expire = 0;
                } else {
                    $expire = null;
                }
                $newmodules[$name][$func]['cacheexpire'] = $expire;
            }
        }
        // save settings to modules in case xarcachemanager is removed later
        xarModVars::set('modules', 'modulecache_settings', serialize($newmodules));

        // modules could be anywhere, we're not smart enough not know exactly where yet
        $key = '';
        // so just flush all pages
        if (xarOutputCache::$pageCacheIsEnabled) {
            xarPageCache::flushCached($key);
        }
        // and flush the modules
        xarModuleCache::flushCached($key);
        if (xarModVars::get('xarcachemanager', 'AutoRegenSessionless')) {
            xarMod::apiFunc('xarcachemanager', 'admin', 'regenstatic');
        }
    }

    // Get all module caching configurations
    $data['modules'] = xarMod::apiFunc('xarcachemanager', 'admin', 'getmodules');

    $data['authid'] = xarSec::genAuthKey();
    return $data;
}

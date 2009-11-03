<?php
/**
 * Config object caching
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
/**
 * configure object caching
 */
function xarcachemanager_admin_objects($args)
{
    extract($args);

    if (!xarSecurityCheck('AdminXarCache')) { return; }

    $cacheOutputDir = sys::varpath() . '/cache/output';

    $data = array();
    if (!file_exists($cacheOutputDir . '/cache.objectlevel')) {
        $data['objects'] = array();
        return $data;
    }

    xarVarFetch('submit','str',$submit,'');
    if (!empty($submit)) {
        // Confirm authorisation code
        if (!xarSecConfirmAuthKey()) return;

        xarVarFetch('docache','isset',$docache,array());
        xarVarFetch('usershared','isset',$usershared,array());
        xarVarFetch('cacheexpire','isset',$cacheexpire,array());

        $newobjects = array();
        // loop over something that should return values for every object
        foreach ($cacheexpire as $name => $expirelist) {
            $newobjects[$name] = array();
            foreach ($expirelist as $method => $expire) {
                $newobjects[$name][$method] = array();
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
                    $expire = xarMod::apiFunc('xarcachemanager', 'admin', 'convertseconds',
                                              array('starttime' => $expire,
                                                    'direction' => 'to'));
                } elseif ($expire === '0') {
                    $expire = 0;
                } else {
                    $expire = NULL;
                }
                $newobjects[$name][$method]['cacheexpire'] = $expire;
            }
        }
        // save settings to dynamicdata in case xarcachemanager is removed later
        xarModVars::set('dynamicdata','objectcache_settings', serialize($newobjects));

        // make sure we can flush objects, even if caching is currently disabled
        if (!xarCache::$outputCacheIsEnabled) {
            sys::import('xaraya.caching.output');
            //xarCache::$outputCacheIsEnabled = xarOutputCache::init();
            xarOutputCache::init();
        }

        // get the caching config settings from the config file
        $config = xarMod::apiFunc('xarcachemanager', 'admin', 'get_cachingconfig',
                                  array('from' => 'file'));

        // objects could be anywhere, we're not smart enough not know exactly where yet
        $key = '';
        // just flush the objects
        if (!xarOutputCache::$objectCacheIsEnabled) {
            sys::import('xaraya.caching.output.object');
            xarObjectCache::init($config);
        }
        xarobjectCache::flushCached($key);
        if (xarModVars::get('xarcachemanager','AutoRegenSessionless')) {
            xarMod::apiFunc( 'xarcachemanager', 'admin', 'regenstatic');
        }
    }

    // Get all object caching configurations
    $data['objects'] = xarModAPIfunc('xarcachemanager', 'admin', 'getobjects');

    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

?>

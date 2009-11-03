<?php
/**
 * Config module caching
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
 * configure module caching
 */
function xarcachemanager_admin_modules($args)
{
    extract($args);

    if (!xarSecurityCheck('AdminXarCache')) { return; }

    $cacheOutputDir = sys::varpath() . '/cache/output';

    $data = array();
    if (!file_exists($cacheOutputDir . '/cache.modulelevel')) {
        $data['modules'] = array();
        return $data;
    }

    xarVarFetch('submit','str',$submit,'');
    if (!empty($submit)) {
        // Confirm authorisation code
        if (!xarSecConfirmAuthKey()) return;

        xarVarFetch('docache','isset',$docache,array());
        xarVarFetch('usershared','isset',$usershared,array());
        xarVarFetch('params','isset',$params,array());
        xarVarFetch('cacheexpire','isset',$cacheexpire,array());

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
                    $expire = xarMod::apiFunc('xarcachemanager', 'admin', 'convertseconds',
                                              array('starttime' => $expire,
                                                    'direction' => 'to'));
                } elseif ($expire === '0') {
                    $expire = 0;
                } else {
                    $expire = NULL;
                }
                $newmodules[$name][$func]['cacheexpire'] = $expire;
            }
        }
        // save settings to modules in case xarcachemanager is removed later
        xarModVars::set('modules','modulecache_settings', serialize($newmodules));

        // make sure we can flush modules, even if caching is currently disabled
        if (!xarCache::$outputCacheIsEnabled) {
            sys::import('xaraya.caching.output');
            //xarCache::$outputCacheIsEnabled = xarOutputCache::init();
            xarOutputCache::init();
        }

        // get the caching config settings from the config file
        $config = xarMod::apiFunc('xarcachemanager', 'admin', 'get_cachingconfig',
                                  array('from' => 'file'));

        // modules could be anywhere, we're not smart enough not know exactly where yet
        $key = '';
        // just flush the modules
        if (!xarOutputCache::$moduleCacheIsEnabled) {
            sys::import('xaraya.caching.output.module');
            xarModuleCache::init($config);
        }
        xarModuleCache::flushCached($key);
        if (xarModVars::get('xarcachemanager','AutoRegenSessionless')) {
            xarMod::apiFunc( 'xarcachemanager', 'admin', 'regenstatic');
        }
    }

    // Get all module caching configurations
    $data['modules'] = xarModAPIfunc('xarcachemanager', 'admin', 'getmodules');

    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

?>

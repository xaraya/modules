<?php
/**
 * Flush output cache
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
 * Flush cache files for a given cacheKey
 * @param flushkey
 * @param string confirm
 * @author jsb
 */
function xarcachemanager_admin_flushcache($args)
{
    // Security Check
    if (!xarSecurityCheck('AdminXarCache')) return;

    extract($args);

    if (!xarVarFetch('flushkey', 'isset', $flushkey, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    $cachetypes = xarMod::apiFunc('xarcachemanager','admin','getcachetypes');

    //Make sure xarOutputCache is included so you delete cacheKeys even if caching is disabled
    if (!xarCache::$outputCacheIsEnabled) {
        sys::import('xaraya.caching.output');
        //xarCache::$outputCacheIsEnabled = xarOutputCache::init();
        xarOutputCache::init();
    }

    if (empty($confirm)) {

        $data = array();

        $data['message']    = false;
        $data['cachetypes'] = $cachetypes;
        $data['cachekeys'] = array();
        foreach (array_keys($cachetypes) as $type) {
            $data['cachekeys'][$type] = xarMod::apiFunc('xarcachemanager', 'admin', 'getcachekeys', $type);
        }

        $data['instructions'] = xarML("Please select a cache key to be flushed.");
        $data['instructionhelp'] = xarML("All cached files of output associated with this key will be deleted.");

        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();

    } else {

        // Confirm authorisation code.
        if (!xarSecConfirmAuthKey()) return;

        //Make sure their is an authkey selected
        if (empty($flushkey) || !is_array($flushkey)) {
            $data['notice'] = xarML("You must select a cache key to flush.  If there is no cache key to select the output cache is empty.");

        } else {

            // get the caching config settings from the config file
            $config = xarMod::apiFunc('xarcachemanager', 'admin', 'get_cachingconfig',
                                      array('from' => 'file'));

            // see if we need to delete an individual item instead of flushing the key
            if (!xarVarFetch('cachecode', 'isset', $cachecode, '', XARVAR_NOT_REQUIRED)) return;

            $found = 0;
            foreach ($flushkey as $type => $key) {
                if (empty($key) || $key == '-') continue;
                if ($key == '*') {
                    $key = '';
                }
                switch($type)
                {
                    case 'page':
                        if (!xarOutputCache::$pageCacheIsEnabled) {
                            sys::import('xaraya.caching.output.page');
                            xarPacheCache::init($config);
                        }
                        if (!empty($key) && !empty($cachecode) && !empty($cachecode[$type]) && !empty(xarPageCache::$cacheStorage)) {
                            xarPageCache::$cacheStorage->setCode($cachecode[$type]);
                            xarPageCache::$cacheStorage->delCached($key);
                        } else {
                            xarPageCache::flushCached($key);
                        }
                        $found++;
                        break;
                    case 'block':
                        if (!xarOutputCache::$blockCacheIsEnabled) {
                            sys::import('xaraya.caching.output.block');
                            xarBlockCache::init($config);
                        }
                        if (!empty($key) && !empty($cachecode) && !empty($cachecode[$type]) && !empty(xarBlockCache::$cacheStorage)) {
                            xarBlockCache::$cacheStorage->setCode($cachecode[$type]);
                            xarBlockCache::$cacheStorage->delCached($key);
                        } else {
                           xarBlockCache::flushCached($key);
                        }
                        $found++;
                        break;
                    case 'module':
                        if (!xarOutputCache::$moduleCacheIsEnabled) {
                            sys::import('xaraya.caching.output.module');
                            xarModuleCache::init($config);
                        }
                        if (!empty($key) && !empty($cachecode) && !empty($cachecode[$type]) && !empty(xarModuleCache::$cacheStorage)) {
                            xarModuleCache::$cacheStorage->setCode($cachecode[$type]);
                            xarModuleCache::$cacheStorage->delCached($key);
                        } else {
                            xarModuleCache::flushCached($key);
                        }
                        $found++;
                        break;
                    case 'object':
                        if (!xarOutputCache::$objectCacheIsEnabled) {
                            sys::import('xaraya.caching.output.object');
                            xarObjectCache::init($config);
                        }
                        if (!empty($key) && !empty($cachecode) && !empty($cachecode[$type]) && !empty(xarObjectCache::$cacheStorage)) {
                            xarObjectCache::$cacheStorage->setCode($cachecode[$type]);
                            xarObjectCache::$cacheStorage->delCached($key);
                        } else {
                            xarObjectCache::flushCached($key);
                        }
                        $found++;
                        break;
                }
            }
            if (empty($found)) {
                $data['notice'] = xarML("You must select a cache key to flush.  If there is no cache key to select the output cache is empty.");
            } else {
                $data['notice'] = xarML("The cached output for this key has been flushed.");
            }
        }

        if (!xarVarFetch('return_url', 'isset', $return_url, NULL, XARVAR_NOT_REQUIRED)) return;
        if (!empty($return_url)) {
            xarResponse::Redirect($return_url);
            return;
        }

        $return_url = xarModURL('xarcachemanager', 'admin', 'flushcache');
        $data['returnlink'] = Array('url'   => $return_url,
                                    'title' => xarML('Return to the cache key selector'),
                                    'label' => xarML('Back'));

        $data['message'] = true;
    }

    $data['cachesize'] = array();
    foreach (array_keys($cachetypes) as $type) {
        $cachesize = xarMod::apiFunc('xarcachemanager', 'admin', 'getcachesize', $type);
        if (!empty($cachesize)) {
            $data['cachesize'][$type] = round($cachesize / 1048576, 2);
        } else {
            $data['cachesize'][$type] = '0.00';
        }
    }

    return $data;
}
?>

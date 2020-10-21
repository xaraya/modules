<?php
/**
 * Get cache size
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
 * @author jsb
 *
 * @param $type cachetype to get the size for
 * @return int size of the cache
*/

function xarcachemanager_adminapi_getcachesize($type = '')
{
    $cachesize = 0;

    // get cache type settings
    $cachetypes = xarMod::apiFunc('xarcachemanager', 'admin', 'getcachetypes');

    // check if we have some settings for this cache type
    if (empty($type) || empty($cachetypes[$type])) {
        return $cachesize;
    }

    // Get the output cache directory so you can get cache size even if output caching is disabled
    $outputCacheDir = xarCache::getOutputCacheDir();

    // default cache storage is 'filesystem' if necessary
    if (!empty($cachetypes[$type]['CacheStorage'])) {
        $storage = $cachetypes[$type]['CacheStorage'];
    } else {
        $storage = 'filesystem';
    }

    // get cache storage
    $cachestorage = xarCache::getStorage(array('storage'  => $storage,
                                               'type'     => $type,
                                               'cachedir' => $outputCacheDir));
    if (empty($cachestorage)) {
        return $cachesize;
    }

    // get cache size
    $cachesize = $cachestorage->getCacheSize();

    return $cachesize;
}

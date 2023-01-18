<?php
/**
 * Construct an array of current cache info
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.xarcachemanager.class.manager');

/**
 * Construct an array of the current cache info
 *
 * @author jsb
 *
 * @param array $args['type'] cachetype to start the search for cacheinfo
 * @return array array of cacheinfo
*/

function xarcachemanager_adminapi_getcacheinfo($args = ['type' => ''])
{
    $type = '';
    if (is_array($args)) {
        extract($args);
    } else {
        $type = $args;
    }
    $cacheinfo = [];

    // get cache type settings
    $cachetypes = xarMod::apiFunc('xarcachemanager', 'admin', 'getcachetypes');

    // check if we have some settings for this cache type
    if (empty($type) || empty($cachetypes[$type])) {
        return $cacheinfo;
    }

    // Get the output cache directory so you can get cache keys even if output caching is disabled
    $outputCacheDir = xarCache::getOutputCacheDir();

    // default cache storage is 'filesystem' if necessary
    if (!empty($cachetypes[$type]['CacheStorage'])) {
        $storage = $cachetypes[$type]['CacheStorage'];
    } else {
        $storage = 'filesystem';
    }

    // get cache storage
    $cachestorage = xarCache::getStorage(['storage'  => $storage,
                                          'type'     => $type,
                                          'cachedir' => $outputCacheDir, ]);
    if (empty($cachestorage)) {
        return $cacheinfo;
    }

    // get cache info
    $cacheinfo = $cachestorage->getCacheInfo();
    $cacheinfo['total'] = $cacheinfo['hits'] + $cacheinfo['misses'];
    if (!empty($cacheinfo['total'])) {
        $cacheinfo['ratio'] = sprintf("%.1f", 100.0 * $cacheinfo['hits'] / $cacheinfo['total']);
    } else {
        $cacheinfo['ratio'] = 0.0;
    }
    if (!empty($cacheinfo['size'])) {
        $cacheinfo['size'] = round($cacheinfo['size'] / 1048576, 2);
    }
    $cacheinfo['storage'] = $storage;

    return $cacheinfo;
}

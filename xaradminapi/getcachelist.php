<?php
/**
 * Construct an array of current cache items
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
 * Construct an array of the current cache items
 *
 * @author jsb
 *
 * @param array $args['type'] cachetype to get the cache items from
 * @return array array of cache items
*/

function xarcachemanager_adminapi_getcachelist($args = ['type' => ''])
{
    $type = '';
    if (is_array($args)) {
        extract($args);
    } else {
        $type = $args;
    }
    $items = [];

    // get cache type settings
    $cachetypes = xarMod::apiFunc('xarcachemanager', 'admin', 'getcachetypes');

    // check if we have some settings for this cache type
    if (empty($type) || empty($cachetypes[$type])) {
        return $items;
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
        return $items;
    }

    // get cache items
    $items = $cachestorage->getCachedList();

    // sort items
    if (empty($sort) || $sort == 'id') {
        $sort = null;
        ksort($items);
    } else {
        sys::import('modules.xarcachemanager.xaradmin.stats');
        xarcachemanager_stats_sortitems($items, $sort);
    }

    return $items;
}

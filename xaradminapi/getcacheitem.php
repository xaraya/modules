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
sys::import('modules.xarcachemanager.class.cache_manager');

/**
 * Construct an array of the current cache info
 *
 * @author jsb
 *
 * @param $type cachetype to start the search for cacheinfo
 * @return array array of cacheinfo
*/

function xarcachemanager_adminapi_getcacheitem($type = '')
{
    if (is_array($type)) {
        extract($type);
    }
    $item = [];

    // get cache type settings
    $cachetypes = xarMod::apiFunc('xarcachemanager', 'admin', 'getcachetypes');

    // check if we have some settings for this cache type
    if (empty($type) || empty($cachetypes[$type])) {
        return $item;
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
        return $item;
    }

    // specify suffix if necessary
    if (!empty($code)) {
        $cachestorage->setCode($code);
    }
    if ($cachestorage->isCached($key, 0, 0)) {
        $value = $cachestorage->getCached($key);
        if ($type == 'module' || $type == 'object') {
            $item = unserialize($value);
        } elseif ($type == 'variable') {
            // check if we serialized it for storage
            if (!empty($value) && is_string($value) && strpos($value, ':serial:') === 0) {
                try {
                    $item = unserialize(substr($value, 8));
                } catch (Exception $e) {
                    $item = $value;
                }
            }
        } else {
            // do nothing
            $item = $value;
        }
    }

    return $item;
}

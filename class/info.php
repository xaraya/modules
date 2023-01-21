<?php
/**
 * Classes to provide info on the cache system of Xaraya
 *
 * @package modules\xarcachemanager
 * @subpackage xarcachemanager
 * @category Xaraya Web Applications Framework
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.info/index.php/release/182.html
 *
 * @author mikespub <mikespub@xaraya.com>
**/

namespace Xaraya\Modules\CacheManager;

use xarObject;
use xarCache;
use Throwable;
use sys;

sys::import('modules.xarcachemanager.class.config');

class CacheInfo extends xarObject
{
    // list of currently supported cache types - not including 'query', 'core', 'template' here
    public static $typelist = ['page', 'block', 'module', 'object', 'variable'];

    public static function init(array $args = [])
    {
    }

    /**
     * Get the cache storage used by a particular cache type
     */
    protected static function getCacheStorage($type)
    {
        // get cache type settings
        $cachetypes = CacheConfig::getTypes();

        // check if we have some settings for this cache type
        if (empty($type) || empty($cachetypes[$type])) {
            return;
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
        $cachestorage = xarCache::getStorage([
            'storage'  => $storage,
            'type'     => $type,
            'cachedir' => $outputCacheDir,
        ]);

        return $cachestorage;
    }

    /**
     * Construct an array of the current cache info
     *
     * @author jsb
     *
     * @param string $type cachetype to start the search for cacheinfo
     * @return array array of cacheinfo
    */
    public static function getInfo($type)
    {
        $cacheinfo = [];

        // get cache storage
        $cachestorage = static::getCacheStorage($type);
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
        $cacheinfo['storage'] = $cachestorage->storage;

        return $cacheinfo;
    }

    /**
     * @author jsb
     *
     * @param string $type cachetype to get the size for
     * @return int size of the cache
    */
    public static function getSize($type)
    {
        $cachesize = 0;

        // get cache storage
        $cachestorage = static::getCacheStorage($type);
        if (empty($cachestorage)) {
            return $cachesize;
        }

        // get cache size
        $cachesize = $cachestorage->getCacheSize();

        return $cachesize;
    }

    /**
     * Construct an array of the current cache items
     *
     * @author jsb
     *
     * @param string $type cachetype to get the cache items from
     * @return array array of cache items
    */
    public static function getList($type)
    {
        $items = [];

        // get cache storage
        $cachestorage = static::getCacheStorage($type);
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

    /**
     * Construct an array of the current cache keys
     *
     * @author jsb
     *
     * @param string $type cachetype to get the cache keys from
     * @return array sorted array of cachekeys
    */
    public static function getKeys($type)
    {
        $cachekeys = [];

        // get cache storage
        $cachestorage = static::getCacheStorage($type);
        if (empty($cachestorage)) {
            return $cachekeys;
        }

        // get cache keys
        $cachekeys = $cachestorage->getCachedKeys();

        // sort keys
        ksort($cachekeys);

        return $cachekeys;
    }

    /**
     * Construct an array of the current cache item
     *
     * @author jsb
     *
     * @param string $type cachetype to get the cache item from
     * @param string $key the cache key
     * @param string $code the cache code (optional)
     * @return array array of cacheitem
    */
    public static function getItem($type, $key, $code = '')
    {
        $item = [];

        // get cache storage
        $cachestorage = static::getCacheStorage($type);
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
                    } catch (Throwable $e) {
                        return $e->getMessage();
                        //$item = $value;
                    }
                } else {
                    $item = $value;
                }
            } else {
                // do nothing
                $item = $value;
            }
        }

        return $item;
    }
}

<?php
/**
 * Get cache size
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
 * @author jsb
 *
 * @param $type cachetype to get the size for
 * @return int size of the cache
*/

function xarcachemanager_adminapi_getcachesize($type = '')
{
    $cachesize = 0;

    // get cache type settings
    $cachetypes = xarModAPIFunc('xarcachemanager','admin','getcachetypes');

    // check if we have some settings for this cache type
    if (empty($type) || empty($cachetypes[$type])) {
        return $cachesize;
    }

    // get default cache directory
    global $xarOutput_cacheCollection;
    if (!empty($xarOutput_cacheCollection)) {
        $cachedir = $xarOutput_cacheCollection;
    } else {
        $cachedir = xarCore_getVarDirPath() . '/cache/output';
    }

    // default cache storage is 'filesystem' if necessary
    if (!empty($cachetypes[$type]['CacheStorage'])) {
        $storage = $cachetypes[$type]['CacheStorage'];
    } else {
        $storage = 'filesystem';
    }

    // get cache storage
    $cachestorage = xarCache_getStorage(array('storage'  => $storage,
                                              'type'     => $type,
                                              'cachedir' => $cachedir));
    if (empty($cachestorage)) {
        return $cachesize;
    }

    // get cache size
    $cachesize = $cachestorage->getCacheSize();

    return $cachesize;
}
?>

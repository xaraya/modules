<?php

/**
 * Construct and array of the current cache keys
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 Xaraya
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @author jsb
 *
 * @param $type cachetype to start the search for cachekeys
 * @returns array
 * @return sorted array of cachekeys
*/

function xarcachemanager_adminapi_getcachekeys($type = '')
{
    $cachekeys = array();

    // get cache type settings
    $cachetypes = xarModAPIFunc('xarcachemanager','admin','getcachetypes');

    // check if we have some settings for this cache type
    if (empty($type) || empty($cachetypes[$type])) {
        return $cachekeys;
    }

    // get default cache directory
    global $xarOutput_cacheCollection;
    if (!empty($xarOutput_cacheCollection)) {
        $cachedir = $xarOutput_cacheCollection;
    } else {
        $cachedir = xarCache_getVarDirPath() . '/cache/output';
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
        return $cachekeys;
    }

    // get cache keys
    $cachekeys = $cachestorage->getCachedKeys();

    sort($cachekeys);

    return $cachekeys;
}
?>

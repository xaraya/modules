<?php
/**
 * View cache items
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
 * show the content of cache items
 */
function xarcachemanager_admin_view($args)
{
    extract($args);

    if (!xarVarFetch('tab',  'str', $tab,  NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('key',  'str', $key,  NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('code', 'str', $code, NULL, XARVAR_NOT_REQUIRED)) {return;}

    if (empty($tab)) {
        xarResponse::Redirect(xarModURL('xarcachemanager','admin','stats'));
        return;
    } elseif (empty($key)) {
        xarResponse::Redirect(xarModURL('xarcachemanager','admin','stats', array('tab' => $tab)));
        return;
    }

    if (!xarSecurityCheck('AdminXarCache')) return;

    $varCacheDir = sys::varpath() . '/cache';
    $outputCacheDir = $varCacheDir . '/output';

    //Make sure xarOutputCache is included so you can view items even if caching is disabled
    if (!xarCache::$outputCacheIsEnabled) {
        sys::import('xaraya.caching.output');
        //xarCache::$outputCacheIsEnabled = xarOutputCache::init();
        xarOutputCache::init();
    }

    $data = array();

    // get the caching config settings from the config file
    $data['settings'] = xarMod::apiFunc('xarcachemanager', 'admin', 'get_cachingconfig',
                                      array('from' => 'file', 'tpl_prep' => TRUE));

    $data['PageCachingEnabled'] = 0;
    $data['BlockCachingEnabled'] = 0;
    $data['ModuleCachingEnabled'] = 0;
    $data['ObjectCachingEnabled'] = 0;
    $data['AutoCachingEnabled'] = 0;
    if (xarOutputCache::$pageCacheIsEnabled) {
        $data['PageCachingEnabled'] = 1;
        if (file_exists($outputCacheDir . '/autocache.log')) {
            $data['AutoCachingEnabled'] = 1;
        }
    }
    if (xarOutputCache::$blockCacheIsEnabled) {
        $data['BlockCachingEnabled'] = 1;
    }
    if (xarOutputCache::$moduleCacheIsEnabled) {
        $data['ModuleCachingEnabled'] = 1;
    }
    if (xarOutputCache::$objectCacheIsEnabled) {
        $data['ObjectCachingEnabled'] = 1;
    }

    $upper = ucfirst($tab);
    $enabled = $upper . 'CachingEnabled'; // e.g. PageCachingEnabled
    $storage = $upper . 'CacheStorage'; // e.g. BlockCacheStorage
    $logfile = $upper . 'LogFile'; // e.g. ModuleLogFile

    $data['tab'] = $tab;
    $data['key'] = $key;
    $data['code'] = $code;
    $data['lines'] = array();
    $data['title']  = '';
    $data['styles'] = array();
    $data['script'] = array();
    if (!empty($data[$enabled]) && !empty($data['settings'][$storage])) {
        // get cache storage
        $cachestorage = xarCache_getStorage(array('storage'  => $data['settings'][$storage],
                                                  'type'     => $tab,
                                                  'cachedir' => $outputCacheDir));
        // specify suffix if necessary
        if (!empty($code)) {
            $cachestorage->setCode($code);
        }
        if ($cachestorage->isCached($key, 0, 0)) {
            $value = $cachestorage->getCached($key);
            if ($tab == 'module') {
                $content = unserialize($value);
                $data['lines']  = explode("\n", $content['output']);
                $data['title']  = $content['title'];
                $data['styles'] = $content['styles'];
                $data['script'] = $content['script'];
            } else {
                $data['lines'] = explode("\n", $value);
            }
        }
    }
    return $data;
}

?>

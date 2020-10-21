<?php
/**
 * Update configuration
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.xarcachemanager.class.cache_manager');

/**
 * Update the configuration parameters of the module based on data from the modification form
 *
 * @return bool true on success of update
 */
function xarcachemanager_admin_updateconfig()
{
    // Get parameters
    if (!xarVar::fetch('cacheenabled', 'isset', $cacheenabled, 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('cachetheme', 'str::24', $cachetheme, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('cachesizelimit', 'float:0.25:', $cachesizelimit, 2, xarVar::NOT_REQUIRED)) {
        return;
    }

    if (!xarVar::fetch('cachepages', 'isset', $cachepages, 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('pageexpiretime', 'str:1:9', $pageexpiretime, '00:30:00', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('pagedisplayview', 'int:0:1', $pagedisplayview, 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('pagetimestamp', 'int:0:1', $pagetimestamp, 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('expireheader', 'int:0:1', $expireheader, 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('pagehookedonly', 'int:0:1', $pagehookedonly, 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('autoregenerate', 'isset', $autoregenerate, 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('pagecachestorage', 'str:1', $pagecachestorage, 'filesystem', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('pagelogfile', 'str', $pagelogfile, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('pagesizelimit', 'float:0.25:', $pagesizelimit, 2, xarVar::NOT_REQUIRED)) {
        return;
    }

    if (!xarVar::fetch('cacheblocks', 'isset', $cacheblocks, 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('blockexpiretime', 'str:1:9', $blockexpiretime, '0', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('blockcachestorage', 'str:1', $blockcachestorage, 'filesystem', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('blocklogfile', 'str', $blocklogfile, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('blocksizelimit', 'float:0.25:', $blocksizelimit, 2, xarVar::NOT_REQUIRED)) {
        return;
    }

    if (!xarVar::fetch('cachemodules', 'isset', $cachemodules, 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('moduleexpiretime', 'str:1:9', $moduleexpiretime, '02:00:00', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('modulecachestorage', 'str:1', $modulecachestorage, 'filesystem', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('modulelogfile', 'str', $modulelogfile, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('modulesizelimit', 'float:0.25:', $modulesizelimit, 2, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('modulefunctions', 'isset', $modulefunctions, array(), xarVar::NOT_REQUIRED)) {
        return;
    }

    if (!xarVar::fetch('cacheobjects', 'isset', $cacheobjects, 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('objectexpiretime', 'str:1:9', $objectexpiretime, '02:00:00', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('objectcachestorage', 'str:1', $objectcachestorage, 'filesystem', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('objectlogfile', 'str', $objectlogfile, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('objectsizelimit', 'float:0.25:', $objectsizelimit, 2, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('objectmethods', 'isset', $objectmethods, array(), xarVar::NOT_REQUIRED)) {
        return;
    }

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) {
        return;
    }
    // Security Check
    if (!xarSecurityCheck('AdminXarCache')) {
        return;
    }

    // set the cache dir
    $varCacheDir = sys::varpath() . '/cache';
    $outputCacheDir = $varCacheDir . '/output';

    // turn output caching system on or off
    if (!empty($cacheenabled)) {
        if (!file_exists($outputCacheDir . '/cache.touch')) {
            touch($outputCacheDir . '/cache.touch');
        }
    } else {
        if (file_exists($outputCacheDir . '/cache.touch')) {
            unlink($outputCacheDir . '/cache.touch');
        }
    }

    // turn page level output caching on or off
    if (!empty($cachepages)) {
        if (!file_exists($outputCacheDir . '/cache.pagelevel')) {
            touch($outputCacheDir . '/cache.pagelevel');
        }
        if (!empty($pagelogfile) && !file_exists($pagelogfile)) {
            touch($pagelogfile);
        }
    } else {
        if (file_exists($outputCacheDir . '/cache.pagelevel')) {
            unlink($outputCacheDir . '/cache.pagelevel');
        }
        if (file_exists($outputCacheDir . '/autocache.start')) {
            unlink($outputCacheDir . '/autocache.start');
        }
        if (file_exists($outputCacheDir . '/autocache.log')) {
            unlink($outputCacheDir . '/autocache.log');
        }
    }

    // turn block level output caching on or off
    if ($cacheblocks) {
        if (!file_exists($outputCacheDir . '/cache.blocklevel')) {
            touch($outputCacheDir . '/cache.blocklevel');
        }
        if (!empty($blocklogfile) && !file_exists($blocklogfile)) {
            touch($blocklogfile);
        }
    } else {
        if (file_exists($outputCacheDir . '/cache.blocklevel')) {
            unlink($outputCacheDir . '/cache.blocklevel');
        }
    }

    // turn module level output caching on or off
    if ($cachemodules) {
        if (!file_exists($outputCacheDir . '/cache.modulelevel')) {
            touch($outputCacheDir . '/cache.modulelevel');
        }
        if (!empty($modulelogfile) && !file_exists($modulelogfile)) {
            touch($modulelogfile);
        }
    } else {
        if (file_exists($outputCacheDir . '/cache.modulelevel')) {
            unlink($outputCacheDir . '/cache.modulelevel');
        }
    }

    // turn object level output caching on or off
    if ($cacheobjects) {
        if (!file_exists($outputCacheDir . '/cache.objectlevel')) {
            touch($outputCacheDir . '/cache.objectlevel');
        }
        if (!empty($objectlogfile) && !file_exists($objectlogfile)) {
            touch($objectlogfile);
        }
    } else {
        if (file_exists($outputCacheDir . '/cache.objectlevel')) {
            unlink($outputCacheDir . '/cache.objectlevel');
        }
    }

    // convert size limit from MB to bytes
    $cachesizelimit = (intval($cachesizelimit * 1048576));
    $pagesizelimit = (intval($pagesizelimit * 1048576));
    $blocksizelimit = (intval($blocksizelimit * 1048576));
    $modulesizelimit = (intval($modulesizelimit * 1048576));
    $objectsizelimit = (intval($objectsizelimit * 1048576));

    //turn hh:mm:ss back into seconds
    $pageexpiretime = xarCache_Manager::convertseconds(
        array('starttime' => $pageexpiretime,
                                        'direction' => 'to')
    );
    $blockexpiretime = xarCache_Manager::convertseconds(
        array('starttime' => $blockexpiretime,
                                       'direction' => 'to')
    );
    $moduleexpiretime = xarCache_Manager::convertseconds(
        array('starttime' => $moduleexpiretime,
                                       'direction' => 'to')
    );
    $objectexpiretime = xarCache_Manager::convertseconds(
        array('starttime' => $objectexpiretime,
                                       'direction' => 'to')
    );

    // updated the config.caching settings
    $cachingConfigFile = $varCacheDir . '/config.caching.php';

    $configSettings = array();
    $configSettings['Output.DefaultTheme'] = $cachetheme;
    $configSettings['Output.SizeLimit'] = $cachesizelimit;
    $configSettings['Output.CookieName'] = xarConfigVars::get(null, 'Site.Session.CookieName');
    if (empty($configSettings['Output.CookieName'])) {
        $configSettings['Output.CookieName'] = 'XARAYASID';
    }
    $configSettings['Output.DefaultLocale'] = xarMLSGetSiteLocale();
    $configSettings['Page.TimeExpiration'] = $pageexpiretime;
    $configSettings['Page.DisplayView'] = $pagedisplayview;
    $configSettings['Page.ShowTime'] = $pagetimestamp;
    $configSettings['Page.ExpireHeader'] = $expireheader;
    $configSettings['Page.HookedOnly'] = $pagehookedonly;
    $configSettings['Page.CacheStorage'] = $pagecachestorage;
    $configSettings['Page.LogFile'] = $pagelogfile;
    $configSettings['Page.SizeLimit'] = $pagesizelimit;

    $configSettings['Block.TimeExpiration'] = $blockexpiretime;
    $configSettings['Block.CacheStorage'] = $blockcachestorage;
    $configSettings['Block.LogFile'] = $blocklogfile;
    $configSettings['Block.SizeLimit'] = $blocksizelimit;

    $configSettings['Module.TimeExpiration'] = $moduleexpiretime;
    $configSettings['Module.CacheStorage'] = $modulecachestorage;
    $configSettings['Module.LogFile'] = $modulelogfile;
    $configSettings['Module.SizeLimit'] = $modulesizelimit;
    // update cache defaults for module functions
    $defaultmodulefunctions = unserialize(xarModVars::get('xarcachemanager', 'DefaultModuleCacheFunctions'));
    foreach ($defaultmodulefunctions as $func => $docache) {
        if (!isset($modulefunctions[$func])) {
            $modulefunctions[$func] = 0;
        }
    }
    $configSettings['Module.CacheFunctions'] = $modulefunctions;
    xarModVars::set('xarcachemanager', 'DefaultModuleCacheFunctions', serialize($modulefunctions));

    $configSettings['Object.TimeExpiration'] = $objectexpiretime;
    $configSettings['Object.CacheStorage'] = $objectcachestorage;
    $configSettings['Object.LogFile'] = $objectlogfile;
    $configSettings['Object.SizeLimit'] = $objectsizelimit;
    // update cache defaults for object methods
    $defaultobjectmethods = unserialize(xarModVars::get('xarcachemanager', 'DefaultObjectCacheMethods'));
    foreach ($defaultobjectmethods as $method => $docache) {
        if (!isset($objectmethods[$method])) {
            $objectmethods[$method] = 0;
        }
    }
    $configSettings['Object.CacheMethods'] = $objectmethods;
    xarModVars::set('xarcachemanager', 'DefaultObjectCacheMethods', serialize($objectmethods));

    xarCache_Manager::save_config(
        array('configSettings' => $configSettings,
                        'cachingConfigFile' => $cachingConfigFile)
    );

    // see if we need to flush the cache when a new comment is added for some item
    xarVar::fetch('pageflushcomment', 'isset', $pageflushcomment, 0, xarVar::NOT_REQUIRED);
    if ($pageflushcomment && $pagedisplayview) {
        xarModVars::set('xarcachemanager', 'FlushOnNewComment', 1);
    } else {
        xarModVars::set('xarcachemanager', 'FlushOnNewComment', 0);
    }

    // see if we need to flush the cache when a new rating is added for some item
    xarVar::fetch('pageflushrating', 'isset', $pageflushrating, 0, xarVar::NOT_REQUIRED);
    if ($pageflushrating  && $pagedisplayview) {
        xarModVars::set('xarcachemanager', 'FlushOnNewRating', 1);
    } else {
        xarModVars::set('xarcachemanager', 'FlushOnNewRating', 0);
    }

    // see if we need to flush the cache when a new vote is cast on a poll hooked to some item
    xarVar::fetch('pageflushpollvote', 'isset', $pageflushpollvote, 0, xarVar::NOT_REQUIRED);
    if ($pageflushpollvote && $pagedisplayview) {
        xarModVars::set('xarcachemanager', 'FlushOnNewPollvote', 1);
    } else {
        xarModVars::set('xarcachemanager', 'FlushOnNewPollvote', 0);
    }

    // set option for auto regeneration of session-less url list cache on event invalidation
    if ($autoregenerate) {
        xarModVars::set('xarcachemanager', 'AutoRegenSessionless', 1);
    } else {
        xarModVars::set('xarcachemanager', 'AutoRegenSessionless', 0);
    }

    // flush adminpanels and base blocks to show new menu options if necessary
    if ($cacheblocks) {
        // get the output cache directory so you can flush items even if output caching is disabled
        $outputCacheDir = xarCache::getOutputCacheDir();

        // get the cache storage for block caching
        $cachestorage = xarCache::getStorage(array('storage'  => $blockcachestorage,
                                                   'type'     => 'block',
                                                   'cachedir' => $outputCacheDir));
        if (!empty($cachestorage)) {
            $cachestorage->flushCached('base-');
            // CHECKME: no longer used ?
            $cachestorage->flushCached('adminpanels-');
        }
    }

    xarResponse::Redirect(xarModURL('xarcachemanager', 'admin', 'modifyconfig'));

    return true;
}

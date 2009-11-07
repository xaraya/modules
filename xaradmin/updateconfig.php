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
/**
 * Update the configuration parameters of the module based on data from the modification form
 *
 * @return bool true on success of update
 */
function xarcachemanager_admin_updateconfig()
{
    // Get parameters
    if (!xarVarFetch('cacheenabled',     'isset',       $cacheenabled,      0,    XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('cachetheme',       'str::24',     $cachetheme,        '',   XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('cachesizelimit',   'float:0.25:', $cachesizelimit,    2,    XARVAR_NOT_REQUIRED)) { return; }

    if (!xarVarFetch('cachepages',       'isset',       $cachepages,        0,    XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('pageexpiretime',   'str:1:9',     $pageexpiretime,    '00:30:00', XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('pagedisplayview',  'int:0:1',     $pagedisplayview,   0,    XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('pagetimestamp',    'int:0:1',     $pagetimestamp,     0,    XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('expireheader',     'int:0:1',     $expireheader,      0,    XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('pagehookedonly',   'int:0:1',     $pagehookedonly,    0,    XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('autoregenerate',   'isset',       $autoregenerate,    0,    XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('pagecachestorage', 'str:1',       $pagecachestorage,  'filesystem', XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('pagelogfile',      'str',         $pagelogfile,       '',   XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('pagesizelimit',    'float:0.25:', $pagesizelimit,     2,    XARVAR_NOT_REQUIRED)) { return; }

    if (!xarVarFetch('cacheblocks',      'isset',       $cacheblocks,       0,    XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('blockexpiretime',  'str:1:9',     $blockexpiretime,   '0',  XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('blockcachestorage','str:1',       $blockcachestorage, 'filesystem', XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('blocklogfile',     'str',         $blocklogfile,      '',   XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('blocksizelimit',   'float:0.25:', $blocksizelimit,    2,    XARVAR_NOT_REQUIRED)) { return; }

    if (!xarVarFetch('cachemodules',      'isset',      $cachemodules,      0,    XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('moduleexpiretime',  'str:1:9',    $moduleexpiretime,  '02:00:00', XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('modulecachestorage','str:1',      $modulecachestorage,'filesystem', XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('modulelogfile',     'str',        $modulelogfile,     '',   XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('modulesizelimit',   'float:0.25:',$modulesizelimit,   2,    XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('modulefunctions',   'isset',      $modulefunctions,   array(), XARVAR_NOT_REQUIRED)) { return; }

    if (!xarVarFetch('cacheobjects',      'isset',      $cacheobjects,      0,    XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('objectexpiretime',  'str:1:9',    $objectexpiretime,  '02:00:00', XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('objectcachestorage','str:1',      $objectcachestorage,'filesystem', XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('objectlogfile',     'str',        $objectlogfile,     '',   XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('objectsizelimit',   'float:0.25:',$objectsizelimit,   2,    XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('objectmethods',     'isset',      $objectmethods,     array(), XARVAR_NOT_REQUIRED)) { return; }

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;
    // Security Check
    if (!xarSecurityCheck('AdminXarCache')) return;

    // set the cache dir
    $varCacheDir = sys::varpath() . '/cache';
    $outputCacheDir = $varCacheDir . '/output';

    // turn output caching system on or off
    if(!empty($cacheenabled)) {
        if(!file_exists($outputCacheDir . '/cache.touch')) {
            touch($outputCacheDir . '/cache.touch');
        }
    } else {
        if(file_exists($outputCacheDir . '/cache.touch')) {
            unlink($outputCacheDir . '/cache.touch');
        }
    }

    // turn page level output caching on or off
    if(!empty($cachepages)) {
        if(!file_exists($outputCacheDir . '/cache.pagelevel')) {
            touch($outputCacheDir . '/cache.pagelevel');
        }
        if (!empty($pagelogfile) && !file_exists($pagelogfile)) {
            touch($pagelogfile);
        }
    } else {
        if(file_exists($outputCacheDir . '/cache.pagelevel')) {
            unlink($outputCacheDir . '/cache.pagelevel');
        }
        if(file_exists($outputCacheDir . '/autocache.start')) {
            unlink($outputCacheDir . '/autocache.start');
        }
        if(file_exists($outputCacheDir . '/autocache.log')) {
            unlink($outputCacheDir . '/autocache.log');
        }
    }

    // turn block level output caching on or off
    if ($cacheblocks) {
        if(!file_exists($outputCacheDir . '/cache.blocklevel')) {
            touch($outputCacheDir . '/cache.blocklevel');
        }
        if (!empty($blocklogfile) && !file_exists($blocklogfile)) {
            touch($blocklogfile);
        }
    } else {
        if(file_exists($outputCacheDir . '/cache.blocklevel')) {
            unlink($outputCacheDir . '/cache.blocklevel');
        }
    }

    // turn module level output caching on or off
    if ($cachemodules) {
        if(!file_exists($outputCacheDir . '/cache.modulelevel')) {
            touch($outputCacheDir . '/cache.modulelevel');
        }
        if (!empty($modulelogfile) && !file_exists($modulelogfile)) {
            touch($modulelogfile);
        }
    } else {
        if(file_exists($outputCacheDir . '/cache.modulelevel')) {
            unlink($outputCacheDir . '/cache.modulelevel');
        }
    }

    // turn object level output caching on or off
    if ($cacheobjects) {
        if(!file_exists($outputCacheDir . '/cache.objectlevel')) {
            touch($outputCacheDir . '/cache.objectlevel');
        }
        if (!empty($objectlogfile) && !file_exists($objectlogfile)) {
            touch($objectlogfile);
        }
    } else {
        if(file_exists($outputCacheDir . '/cache.objectlevel')) {
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
    $pageexpiretime = xarMod::apiFunc( 'xarcachemanager', 'admin', 'convertseconds',
                                 array('starttime' => $pageexpiretime,
                                        'direction' => 'to'));
    $blockexpiretime = xarMod::apiFunc( 'xarcachemanager', 'admin', 'convertseconds',
                                 array('starttime' => $blockexpiretime,
                                       'direction' => 'to'));
    $moduleexpiretime = xarMod::apiFunc( 'xarcachemanager', 'admin', 'convertseconds',
                                 array('starttime' => $moduleexpiretime,
                                       'direction' => 'to'));
    $objectexpiretime = xarMod::apiFunc( 'xarcachemanager', 'admin', 'convertseconds',
                                 array('starttime' => $objectexpiretime,
                                       'direction' => 'to'));

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
    $defaultmodulefunctions = unserialize(xarModVars::get('xarcachemanager','DefaultModuleCacheFunctions'));
    foreach ($defaultmodulefunctions as $func => $docache) {
        if (!isset($modulefunctions[$func])) $modulefunctions[$func] = 0;
    }
    $configSettings['Module.CacheFunctions'] = $modulefunctions;
    xarModVars::set('xarcachemanager','DefaultModuleCacheFunctions', serialize($modulefunctions));

    $configSettings['Object.TimeExpiration'] = $objectexpiretime;
    $configSettings['Object.CacheStorage'] = $objectcachestorage;
    $configSettings['Object.LogFile'] = $objectlogfile;
    $configSettings['Object.SizeLimit'] = $objectsizelimit;
    // update cache defaults for object methods
    $defaultobjectmethods = unserialize(xarModVars::get('xarcachemanager','DefaultObjectCacheMethods'));
    foreach ($defaultobjectmethods as $method => $docache) {
        if (!isset($objectmethods[$method])) $objectmethods[$method] = 0;
    }
    $configSettings['Object.CacheMethods'] = $objectmethods;
    xarModVars::set('xarcachemanager','DefaultObjectCacheMethods', serialize($objectmethods));

    xarMod::apiFunc('xarcachemanager', 'admin', 'save_cachingconfig',
                  array('configSettings' => $configSettings,
                        'cachingConfigFile' => $cachingConfigFile));

    // see if we need to flush the cache when a new comment is added for some item
    xarVarFetch('pageflushcomment','isset',$pageflushcomment,0,XARVAR_NOT_REQUIRED);
    if ($pageflushcomment && $pagedisplayview) {
        xarModVars::set('xarcachemanager','FlushOnNewComment', 1);
    } else {
        xarModVars::set('xarcachemanager','FlushOnNewComment', 0);
    }

    // see if we need to flush the cache when a new rating is added for some item
    xarVarFetch('pageflushrating','isset',$pageflushrating,0,XARVAR_NOT_REQUIRED);
    if ($pageflushrating  && $pagedisplayview) {
        xarModVars::set('xarcachemanager','FlushOnNewRating', 1);
    } else {
        xarModVars::set('xarcachemanager','FlushOnNewRating', 0);
    }

    // see if we need to flush the cache when a new vote is cast on a poll hooked to some item
    xarVarFetch('pageflushpollvote','isset',$pageflushpollvote,0,XARVAR_NOT_REQUIRED);
    if ($pageflushpollvote && $pagedisplayview) {
        xarModVars::set('xarcachemanager','FlushOnNewPollvote', 1);
    } else {
        xarModVars::set('xarcachemanager','FlushOnNewPollvote', 0);
    }

    // set option for auto regeneration of session-less url list cache on event invalidation
    if ($autoregenerate) {
        xarModVars::set('xarcachemanager','AutoRegenSessionless', 1);
    } else {
        xarModVars::set('xarcachemanager','AutoRegenSessionless', 0);
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

?>

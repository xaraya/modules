<?php
/*
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
 */
function xarcachemanager_admin_updateconfig()
{ 
    // Get parameters
    if (!xarVarFetch('cacheenabled',     'isset',       $cacheenabled,      0,    XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('cachetheme',       'str::24',     $cachetheme,        '',   XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('cachesizelimit',   'float:0.25:', $cachesizelimit,    0.25, XARVAR_NOT_REQUIRED)) { return; }

    if (!xarVarFetch('cachepages',       'isset',       $cachepages,        0,    XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('pageexpiretime',   'str:1:9',     $pageexpiretime,    '0',  XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('cachedisplayview', 'int:0:1',     $cachedisplayview,  0,    XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('cachetimestamp',   'int:0:1',     $cachetimestamp,    0,    XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('expireheader',     'int:0:1',     $expireheader,      0,    XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('pagehookedonly',   'int:0:1',     $pagehookedonly,    0,    XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('autoregenerate',   'isset',       $autoregenerate,    0,    XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('pagecachestorage', 'str:1',       $pagecachestorage,  'filesystem', XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('pagelogfile',      'str',         $pagelogfile,       '',   XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('pagesizelimit',    'float:0.25:', $pagesizelimit,     0.25, XARVAR_NOT_REQUIRED)) { return; }

    if (!xarVarFetch('cacheblocks',      'isset',       $cacheblocks,       0,    XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('blockexpiretime',  'str:1:9',     $blockexpiretime,   '0',  XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('blockcachestorage','str:1',       $blockcachestorage, 'filesystem', XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('blocklogfile',     'str',         $blocklogfile,      '',   XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('blocksizelimit',   'float:0.25:', $blocksizelimit,    0.25, XARVAR_NOT_REQUIRED)) { return; }

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return; 
    // Security Check
    if (!xarSecurityCheck('AdminXarCache')) return;

    // set the cache dir
    $varCacheDir = xarCoreGetVarDirPath() . '/cache';
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
        // flush adminpanels blocks to show new options if necessary
        if (!function_exists('xarOutputFlushCached')) {
            include_once('includes/xarCache.php');
            xarCache_init(array('cacheDir' => $outputCacheDir));
        }
        xarOutputFlushCached('adminpanels-block');
    } else {
        if(file_exists($outputCacheDir . '/cache.blocklevel')) {
            unlink($outputCacheDir . '/cache.blocklevel');
        }
    }

    // convert size limit from MB to bytes
    $cachesizelimit = (intval($cachesizelimit * 1048576));
    $pagesizelimit = (intval($pagesizelimit * 1048576));
    $blocksizelimit = (intval($blocksizelimit * 1048576));
    
    //turn hh:mm:ss back into seconds
    $pageexpiretime = xarModAPIFunc( 'xarcachemanager', 'admin', 'convertseconds',
                                 array('starttime' => $pageexpiretime,
                                        'direction' => 'to'));
    $blockexpiretime = xarModAPIFunc( 'xarcachemanager', 'admin', 'convertseconds',
                                 array('starttime' => $blockexpiretime,
                                       'direction' => 'to'));

    // updated the config.caching settings
    $cachingConfigFile = $varCacheDir . '/config.caching.php';
    
    $configSettings = array();
    $configSettings['Output.DefaultTheme'] = $cachetheme;
    $configSettings['Output.SizeLimit'] = $cachesizelimit;
    $configSettings['Page.TimeExpiration'] = $pageexpiretime;
    $configSettings['Page.DisplayView'] = $cachedisplayview;
    $configSettings['Page.ShowTime'] = $cachetimestamp;
    $configSettings['Page.ExpireHeader'] = $expireheader;
    $configSettings['Page.HookedOnly'] = $pagehookedonly;
    $configSettings['Page.CacheStorage'] = $pagecachestorage;
    $configSettings['Page.LogFile'] = $pagelogfile;
    $configSettings['Page.SizeLimit'] = $pagesizelimit;

    $configSettings['Block.TimeExpiration'] = $blockexpiretime;
    $configSettings['Block.CacheStorage'] = $blockcachestorage;
    $configSettings['Block.LogFile'] = $blocklogfile;
    $configSettings['Block.SizeLimit'] = $blocksizelimit;

    xarModAPIFunc('xarcachemanager', 'admin', 'save_cachingconfig', 
                  array('configSettings' => $configSettings,
                        'cachingConfigFile' => $cachingConfigFile));

    // see if we need to flush the cache when a new comment is added for some item
    xarVarFetch('cacheflushcomment','isset',$cacheflushcomment,0,XARVAR_NOT_REQUIRED);
    if ($cacheflushcomment && $cachedisplayview) {
        xarModSetVar('xarcachemanager','FlushOnNewComment', 1);
    } else {
        xarModSetVar('xarcachemanager','FlushOnNewComment', 0);
    }

    // see if we need to flush the cache when a new rating is added for some item
    xarVarFetch('cacheflushrating','isset',$cacheflushrating,0,XARVAR_NOT_REQUIRED);
    if ($cacheflushrating  && $cachedisplayview) {
        xarModSetVar('xarcachemanager','FlushOnNewRating', 1);
    } else {
        xarModSetVar('xarcachemanager','FlushOnNewRating', 0);
    }

    // see if we need to flush the cache when a new vote is cast on a poll hooked to some item
    xarVarFetch('cacheflushpollvote','isset',$cacheflushpollvote,0,XARVAR_NOT_REQUIRED);
    if ($cacheflushpollvote && $cachedisplayview) {
        xarModSetVar('xarcachemanager','FlushOnNewPollvote', 1);
    } else {
        xarModSetVar('xarcachemanager','FlushOnNewPollvote', 0);
    }
    
    // set option for auto regeneration of session-less url list cache on event invalidation
    if ($autoregenerate) {
        xarModSetVar('xarcachemanager','AutoRegenSessionless', 1);
    } else {
        xarModSetVar('xarcachemanager','AutoRegenSessionless', 0);
    }

    xarResponseRedirect(xarModURL('xarcachemanager', 'admin', 'modifyconfig'));

    return true;
}

?>

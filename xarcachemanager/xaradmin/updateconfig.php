<?php

/**
 * Update configuration
 */
function xarcachemanager_admin_updateconfig()
{ 
    // Get parameters
    list(
         $cacheenabled,
         $expiretime,
         $cachetheme,
         $cachedisplayview,
         $cachetimestamp,
         $cachesizelimit
        ) = xarVarCleanFromInput(
                                 'cacheenabled',
                                 'expiretime',
                                 'cachetheme',
                                 'cachedisplayview',
                                 'cachetimestamp',
                                 'cachesizelimit'
                                );
    
    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return; 
    // Security Check
    if (!xarSecurityCheck('AdminXarCache')) return;

    // set the cache dir
    $varCacheDir = xarCoreGetVarDirPath() . '/cache';

    // turn caching on or off
    if(!empty($cacheenabled)) {
        if(!file_exists($varCacheDir . '/output/cache.touch')) {
            touch($varCacheDir . '/output/cache.touch');
        }
    } else {
        if(file_exists($varCacheDir . '/output/cache.touch')) {
            unlink($varCacheDir . '/output/cache.touch');
        }
    }

    $cachesizelimit *= 1048576;
    
    //turn hh:mm:ss back into seconds
    $expiretime = xarModAPIFunc( 'xarcachemanager', 'admin', 'convertseconds',
                                                             array('starttime' => $expiretime,
                                                                   'direction' => 'to'));

    if(!empty($cachedisplayview)) {
        $cachedisplayview = 1;
    } else {
        $cachedisplayview = 0;
    }
    if(!empty($cachetimestamp)) {
        $cachetimestamp = 1;
    } else {
        $cachetimestamp = 0;
    }
    if(empty($cachesizelimit)) {
        $cachesizelimit = 262144;
    }
    
    // updated the config.caching settings
    $cachingConfigFile = $varCacheDir . '/config.caching.php';
    
    if (!is_writable($cachingConfigFile)) {
        $msg=xarML('The caching configuration file is not writable by the web server.  
                   #(1) must be writable by the web server for 
                   the output caching to be managed by xarCacheManager.', $cachingConfigFile);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION,'FUNCTION_FAILED',
                        new SystemException($msg));
        return false;
    }

    $cachingConfig = join('', file($cachingConfigFile));

    $cachingConfig = preg_replace('/\[\'Page.TimeExpiration\'\]\s*=\s*(|\")(.*)\\1;/', "['Page.TimeExpiration'] = $expiretime;", $cachingConfig);
    $cachingConfig = preg_replace('/\[\'Page.DefaultTheme\'\]\s*=\s*(\'|\")(.*)\\1;/', "['Page.DefaultTheme'] = '$cachetheme';", $cachingConfig);
    $cachingConfig = preg_replace('/\[\'Page.DisplayView\'\]\s*=\s*(|\")(.*)\\1;/', "['Page.DisplayView'] = $cachedisplayview;", $cachingConfig);
    $cachingConfig = preg_replace('/\[\'Page.ShowTime\'\]\s*=\s*(|\")(.*)\\1;/', "['Page.ShowTime'] = $cachetimestamp;", $cachingConfig);
    $cachingConfig = preg_replace('/\[\'Output.SizeLimit\'\]\s*=\s*(|\")(.*)\\1;/', "['Output.SizeLimit'] = $cachesizelimit;", $cachingConfig);

    $fp = fopen ($cachingConfigFile, 'wb');
    fwrite ($fp, $cachingConfig);
    fclose ($fp);

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

    // see if we need to flush the cache when a new vote is cast on poll hooked to some item
    xarVarFetch('cacheflushpollvote','isset',$cacheflushpollvote,0,XARVAR_NOT_REQUIRED);
    if ($cacheflushpollvote && $cachedisplayview) {
        xarModSetVar('xarcachemanager','FlushOnNewPollvote', 1);
    } else {
        xarModSetVar('xarcachemanager','FlushOnNewPollvote', 0);
    }

    xarResponseRedirect(xarModURL('xarcachemanager', 'admin', 'modifyconfig'));

    return true;
}

?>

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

    // todo: do this this standard way, check the variable cache
    $systemVar = xarCoreGetVarDirPath();

    // turn caching on or off
    if(!empty($cacheenabled)) {
        if(!file_exists($systemVar . '/cache/output/cache.touch')) {
            touch($systemVar . '/cache/output/cache.touch');
        }
    } else {
        if(file_exists($systemVar . '/cache/output/cache.touch')) {
            unlink($systemVar . '/cache/output/cache.touch');
        }
    }
    
    //turn minutes back into seconds
    $expiretime = $expiretime * 60;

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
        $cachesizelimit = 0.2;
    }
    
    // updated the config.caching settings
    $cachingConfigFile = $systemVar . '/cache/config.caching.php';
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
    if ($cacheflushcomment) {
        xarModSetVar('xarcachemanager','FlushOnNewComment', 1);
    } else {
        xarModSetVar('xarcachemanager','FlushOnNewComment', 0);
    }

    xarResponseRedirect(xarModURL('xarcachemanager', 'admin', 'modifyconfig'));

    return true;
}

?>

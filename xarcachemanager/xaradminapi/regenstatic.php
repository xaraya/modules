<?php

/**
 * regenerate the page output cache of URLs in the session-less list
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 Xaraya
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @author jsb
 *
 * @returns void
 * @return void
*/

function xarcachemanager_adminapi_regenstatic()
{   
    $outputCacheDir = xarCoreGetVarDirPath() . '/cache/output/';

    // make sure output caching is really enabled, and that we are caching pages
    if (!defined('XARCACHE_IS_ENABLED') || !defined('XARCACHE_PAGE_IS_ENABLED')) {
        return;
    }
    
    xarOutputFlushCached('static', $outputCacheDir . 'page');
    $configKeys = array('Page.SessionLess');
    $sessionlessurls = xarModAPIFunc('xarcachemanager', 'admin', 'get_cachingconfig',
                                     array('keys' => $configKeys, 'from' => 'file', 'viahook' => TRUE));
    
    foreach ($sessionlessurls['Page.SessionLess'] as $url) {
        // Make sure the url isn't empty before calling getfile()
        if (strlen(trim($url))) {
            xarModAPIFunc('base', 'user', 'getfile', array('url' => $url));
        }
    }
    return;        
}
?>

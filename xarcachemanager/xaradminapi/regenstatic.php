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

function xarcachemanager_adminapi_regenstatic($nolimit = NULL)
{
    $urls = array();
    $outputCacheDir = xarCoreGetVarDirPath() . '/cache/output/';

    // make sure output caching is really enabled, and that we are caching pages
    if (!defined('XARCACHE_IS_ENABLED') || !defined('XARCACHE_PAGE_IS_ENABLED')) {
        return;
    }
    
    xarOutputFlushCached('static', $outputCacheDir . 'page');
    $configKeys = array('Page.SessionLess');
    $sessionlessurls = xarModAPIFunc('xarcachemanager', 'admin', 'get_cachingconfig',
                                     array('keys' => $configKeys, 'from' => 'file', 'viahook' => TRUE));

    $urls = $sessionlessurls['Page.SessionLess'];
    
    if (!$nolimit) {
        // randomize the order of the urls just in case the timelimit cuts the 
        // process short - no need to always drop the same pages.
        shuffle($urls);

        // set a time limit for the regeneration
        // TODO: make the timelimit variable and configurable.
        $timelimit = time() + 10;
    }

    foreach ($urls as $url) {
        // Make sure the url isn't empty before calling getfile()
        if (strlen(trim($url))) {
            xarModAPIFunc('base', 'user', 'getfile', array('url' => $url));
        }
        if (!$nolimit && time() > $timelimit) break;
    }
    
    return;

}

?>

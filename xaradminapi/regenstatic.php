<?php
/**
 * Regenerate the page output cache of URLs in sessionless list
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
 * regenerate the page output cache of URLs in the session-less list
 * @author jsb
 *
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
            xarModAPIFunc('base', 'user', 'getfile', array('url' => $url, 'superrors' => true));
        }
        if (!$nolimit && time() > $timelimit) break;
    }

    return;

}

?>

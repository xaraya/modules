<?php
/**
 * Flush output cache
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
 * Flush cache files for a given cacheKey
 * @param flushkey
 * @param string confirm
 * @author jsb
 */
function xarcachemanager_admin_flushcache($args)
{
    // Security Check
    if (!xarSecurityCheck('AdminXarCache')) return;

    extract($args);

    if (!xarVarFetch('flushkey', 'isset', $flushkey, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    $cachetypes = xarModAPIFunc('xarcachemanager','admin','getcachetypes');

    //Make sure xarCache is included so you can delete cacheKeys even if caching is disabled
    if (!defined('XARCACHE_IS_ENABLED')) {
        include_once('includes/xarCache.php');
        xarCache_init();
    }

    if (empty($confirm)) {

        $data = array();

        $data['message']    = false;
        $data['cachetypes'] = $cachetypes;
        $data['cachekeys'] = array();
        foreach (array_keys($cachetypes) as $type) {
            $data['cachekeys'][$type] = xarModAPIFunc('xarcachemanager', 'admin', 'getcachekeys', $type);
        }

        $data['instructions'] = xarML("Please select a cache key to be flushed.");
        $data['instructionhelp'] = xarML("All cached files of output associated with this key will be deleted.");

        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();

    } else {

        // Confirm authorisation code.
        if (!xarSecConfirmAuthKey()) return;

        //Make sure their is an authkey selected
        if (empty($flushkey) || !is_array($flushkey)) {
            $data['notice'] = xarML("You must select a cache key to flush.  If there is no cache key to select the output cache is empty.");
        } else {
            foreach ($flushkey as $type => $key) {
                if (empty($key) || $key == '-') continue;
                if ($key == '*') {
                    xarOutputFlushCached('',$type);
                } else {
                    xarOutputFlushCached($key,$type);
                }
            }
            $data['notice'] = xarML("Cached files have been successfully flushed.");
        }

        $data['returnlink'] = Array('url'   => xarModURL('xarcachemanager',
                                                         'admin',
                                                         'flushcache'),
                                    'title' => xarML('Return to the cache key selector'),
                                    'label' => xarML('Back'));

        $data['message'] = true;
    }

    $data['cachesize'] = array();
    foreach (array_keys($cachetypes) as $type) {
        $cachesize = xarModAPIFunc('xarcachemanager', 'admin', 'getcachesize', $type);
        if (!empty($cachesize)) {
            $data['cachesize'][$type] = round($cachesize / 1048576, 2);
        } else {
            $data['cachesize'][$type] = '0.00';
        }
    }

    return $data;
}
?>

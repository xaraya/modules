<?php

/**
 * Flush cache files for a given cacheKey
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 Xaraya
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @author jsb
*/

function xarcachemanager_admin_flushcache($args)
{
    // Security Check
    if (!xarSecurityCheck('AdminXarCache')) return;

    extract($args);

    if (!xarVarFetch('flushkey', 'str', $flushkey, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return; 

    if (empty($confirm)) {

        $data = array();

        $data['message']    = false;
        $data['cachekeys'] = array();

        $handle = opendir(xarCoreGetVarDirPath() . '/cache/output/');
        while ($file = readdir($handle)) {
            if ($file != '.' && $file != '..' && $file !='cache.touch') {
                $ckey = substr($file, 0, (strrpos($file, '-')));
                $data['cachekeys'][$ckey] = array('ckey' => $ckey);
            }
        }
        closedir($handle);
        sort($data['cachekeys']);

        $data['instructions'] = xarML("Please select a cache key to be flushed.");
        $data['instructionhelp'] = xarML("All cached pages of output associated with this key will be deleted.");

        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();

    } else {

        // Confirm authorisation code.
        if (!xarSecConfirmAuthKey()) return;

        //Make sure their is an authkey selected
        if (empty($flushkey)) {
            $data['notice'] = xarML("You must select a cache key to flush.  If there is no cache key to select, there are no output cache files to flush.");
        } else {
            xarPageFlushCached($flushkey);
            $data['notice'] = xarML("Cached " . $flushkey . " pages have been successfully flushed.");
        }
        
        $data['returnlink'] = Array('url'   => xarModURL('xarcachemanager',
                                                         'admin',
                                                         'flushcache'),
                                    'title' => xarML('Return to the cache key selector'),
                                    'label' => xarML('Back'));

        $data['message'] = true;
    }

    return $data;
}
?>

<?php

/**
 * Construct and array of the current cache keys
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 Xaraya
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @author jsb
 *
 * @param $dir directory to start the search for cachekeys in
 * @returns array
 * @return sorted array of cachekeys, with key and value both set to $ckey
*/

function xarcachemanager_adminapi_getcachekeys($dir = FALSE)
{   
    $cachekeys = array();

    if ($dir && is_dir($dir)) {
        if (substr($dir,-1) != "/") $dir .= "/";
        if ($dirId = opendir($dir)) {
            while (($item = readdir($dirId)) !== FALSE) {
                if ($item[0] != '.') {
                    if (is_dir($dir . $item)) {
                        $cachekeys = array_merge($cachekeys, xarcachemanager_adminapi_getcachekeys($dir . $item));
                    } else {
                        if (strpos($item, '.php')) {
                            $ckey = substr($item, 0, (strrpos($item, '-')));
                            if (!empty($ckey)) {
                                $cachekeys[$ckey] = $ckey;
                            }
                        }
                    }
                }
            }
            closedir($dirId);
        }
    }
    asort($cachekeys);
    return $cachekeys;         
}
?>

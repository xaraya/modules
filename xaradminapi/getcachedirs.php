<?php

/**
 * construct an array of output cache subdirectories
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 Xaraya
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @author jsb
 *
 * @param $dir directory to start the search for subdirectories in
 * @returns array
 * @return sorted array of cache sub directories, with key set to directory name and value set to path
 * @todo do not include empty directories in the array
*/

function xarcachemanager_adminapi_getcachedirs($dir = FALSE)
{   
    $cachedirs = array();

    if ($dir && is_dir($dir)) {
        if (substr($dir,-1) != "/") $dir .= "/";
        if ($dirId = opendir($dir)) {
            while (($item = readdir($dirId)) !== FALSE) {
                if ($item[0] != '.') {
                    if (is_dir($dir . $item)) {
                        $cachedirs[$item] = $dir . $item;
                        $cachedirs = array_merge($cachedirs, xarcachemanager_adminapi_getcachedirs($dir . $item));
                    }
                }
            }
            closedir($dirId);
        }
    }
    asort($cachedirs);
    return $cachedirs;         
}
?>

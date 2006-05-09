<?php
/**
 * construct an array of output cache subdirectories
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 * @author jsb
 */
/**
 * construct an array of output cache subdirectories
 *
 * @param $dir directory to start the search for subdirectories in
 * @return array sorted array of cache sub directories, with key set to directory name and value set to path
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

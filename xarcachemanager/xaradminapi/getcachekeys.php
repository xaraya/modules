<?php
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
                                $cachekeys[$ckey] = array('ckey' => $ckey);
                            }
                        }
                    }
                }
            }
            closedir($dirId);
        }
    }
    sort($cachekeys);
    return $cachekeys;         
}
?>
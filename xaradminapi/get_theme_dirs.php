<?php
/**
 * Get directories list from theme directory
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

//  This function returns an array containing all the xt files
//  in a given directory

$staticNames = array();

function searchFiles($path, $prefix, $force=0)
{
    global $staticNames;

    $path2 = mb_ereg_replace($prefix,"",$path);

    if ($force) {
        $staticNames[] = $path2;
        return false;
    }

    $pattern = '/^([a-z0-9\-_]+)\.xt$/i';
    $subnames = xarMod::apiFunc('translations','admin','get_theme_files',
                              array('themedir'=>"$path",'pattern'=>$pattern));
    if (count($subnames) > 0) {
        $staticNames[] = $path2;
        return true;
    }
    return false;
}

function translations_adminapi_get_theme_dirs($args)
{
    global $staticNames;

    // Get arguments
    extract($args);

    // Argument check
    assert('isset($themedir)');
    $prefix = "themes/$themedir/";

    if (file_exists("themes/$themedir")) {
        $dd = opendir("themes/$themedir");
        while ($filename = readdir($dd)) {
            if ($filename == 'blocks' || $filename == 'pages' || $filename == 'includes') {
                searchFiles("themes/$themedir/$filename", $prefix);
            } elseif ($filename == 'modules') {
                searchFiles("themes/$themedir/modules", $prefix, 1);
                $dd2 = opendir("themes/$themedir/modules");
                while ($moddir = readdir($dd2)) {
                    if (($moddir == '.') || ($moddir == '..') || ($moddir == 'SCCS')) continue;
                    if (is_dir(sys::code() . "themes/$themedir/modules/$moddir")) {
                        $force = 0;
                        $filesBlock = false;
                        $filesIncl = false;
                        if (is_dir(sys::code() . "themes/$themedir/modules/$moddir/blocks")) {
                            $filesBlock = searchFiles(sys::code() . "themes/$themedir/modules/$moddir/blocks", $prefix);
                        }
                        if (is_dir(sys::code() . "themes/$themedir/modules/$moddir/includes")) {
                            $filesIncl = searchFiles(sys::code() . "themes/$themedir/modules/$moddir/includes", $prefix);
                        }
                        if (is_dir(sys::code() . "themes/$themedir/modules/$moddir/properties")) {
                            $filesIncl = searchFiles(sys::code() . "themes/$themedir/modules/$moddir/properties", $prefix);
                        }
                        if (is_dir(sys::code() . "themes/$themedir/modules/$moddir/objects")) {
                            $filesIncl = searchFiles(sys::code() . "themes/$themedir/modules/$moddir/objects", $prefix);
                        }
                        if ($filesBlock || $filesIncl) $force = 1;
                        searchFiles(sys::code() . "themes/$themedir/modules/$moddir", $prefix, $force);
                    }
                }
                closedir($dd2);
            }
        }
        closedir($dd);
    }
    sort($staticNames);
    return $staticNames;
}

?>
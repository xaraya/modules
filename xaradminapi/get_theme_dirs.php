<?php
/**
 * Translations Module
 *
 * @package modules
 * @subpackage translations module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/77.html
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
 */

//  This function returns an array containing all the xt files
//  in a given directory

$staticNames = array();

function searchFiles($path, $prefix, $force=0)
{
    global $staticNames;

    $path2 = mb_ereg_replace($prefix, "", $path);

    if ($force) {
        $staticNames[] = $path2;
        return false;
    }

    $pattern = '/^([a-z0-9\-_]+)\.xt$/i';
    $subnames = xarMod::apiFunc(
        'translations',
        'admin',
        'get_theme_files',
        array('themedir'=>"$path",'pattern'=>$pattern)
    );
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
                    if (($moddir == '.') || ($moddir == '..') || ($moddir == 'SCCS')) {
                        continue;
                    }
                    if (is_dir("themes/$themedir/modules/$moddir")) {
                        $force = 0;
                        $filesBlock = false;
                        $filesIncl = false;
                        if (is_dir("themes/$themedir/modules/$moddir/blocks")) {
                            $filesBlock = searchFiles("themes/$themedir/modules/$moddir/blocks", $prefix);
                        }
                        if (is_dir("themes/$themedir/modules/$moddir/includes")) {
                            $filesIncl = searchFiles("themes/$themedir/modules/$moddir/includes", $prefix);
                        }
                        if (is_dir("themes/$themedir/modules/$moddir/properties")) {
                            $filesIncl = searchFiles("themes/$themedir/modules/$moddir/properties", $prefix);
                        }
                        if (is_dir("themes/$themedir/modules/$moddir/objects")) {
                            $filesIncl = searchFiles("themes/$themedir/modules/$moddir/objects", $prefix);
                        }
                        if ($filesBlock || $filesIncl) {
                            $force = 1;
                        }
                        searchFiles("themes/$themedir/modules/$moddir", $prefix, $force);
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

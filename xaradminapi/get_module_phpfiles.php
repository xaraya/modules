<?php
/**
 * Get filenames list from module directory
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

//  This function returns an array containing all the php files
//  in a given directory that start with "xar"
function translations_adminapi_get_module_phpfiles($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert('isset($moddir)');

    $names = array();
    if (file_exists(sys::code() . "modules/$moddir")) {
        $dd = opendir(sys::code() . "modules/$moddir");
        while ($filename = readdir($dd)) {
//            if (is_dir(sys::code() . "modules/$moddir/$filename") && (substr($filename,0,3) == "xar")) {
//                $names[] = mb_ereg_replace("^xar","",$filename);
//                continue;
//            }
            if (!preg_match('!^([a-z\-_]+)\.php$!i', $filename, $matches)) continue;
            $phpname = $matches[1];
//            if ($phpname == 'xarversion') continue;
            if ($phpname == 'xartables') continue;
            $names[] = mb_ereg_replace("^xar","",$phpname);
        }
        closedir($dd);
    }
    return $names;
}

?>
<?php

/**
 * File: $Id$
 *
 * Get directories list from module directory
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
function translations_adminapi_get_module_dirs($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert('isset($moddir)');

    $names = array();
    if (file_exists("modules/$moddir")) {
        $dd = opendir("modules/$moddir");
        while ($filename = readdir($dd)) {
            if (!is_dir("modules/$moddir/$filename")) continue;
            if (substr($filename,0,3) != "xar") continue;
            if ($filename == 'xardocs') continue;
            if ($filename == 'xarimages') continue;
            $names[] = ereg_replace("^xar","",$filename);
        }
        closedir($dd);
    }
    if (file_exists("modules/$moddir/xartemplates")) {
        if (file_exists("modules/$moddir/xartemplates/includes")) 
            $names[] = 'templates/includes';
        if (file_exists("modules/$moddir/xartemplates/blocks")) 
            $names[] = 'templates/blocks';
    }
    return $names;
}

?>
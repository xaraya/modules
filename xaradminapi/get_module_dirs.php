<?php
/**
 * Get directories list from module directory
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/
/**
 *  This function returns an array containing all the php files
 *  in a given directory that start with "xar"
 * @return array
 */
function translations_adminapi_get_module_dirs($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert('isset($moddir)');

    $names = array();
    $dropit = array(
        'xardocs',
        'xarimages',
        'xarclass',
        'xardata',
        'xarwidgets',
        'xartests',
        'xarjava',
        'xarjavascript',
        'xarstyles');
    if (file_exists(sys::code() . "modules/$moddir")) {
        $dd = opendir(sys::code() . "modules/$moddir");
        while ($filename = readdir($dd)) {
            if (!is_dir(sys::code() . "modules/$moddir/$filename")) continue;
            if (substr($filename,0,3) != "xar") continue;
            if (in_array($filename, $dropit)) continue;
            $names[] = mb_ereg_replace("^xar","",$filename);
        }
        closedir($dd);
    }
    if (file_exists(sys::code() . "modules/$moddir/xartemplates")) {
        if (file_exists(sys::code() . "modules/$moddir/xartemplates/includes"))
            $names[] = 'templates/includes';
        if (file_exists(sys::code() . "modules/$moddir/xartemplates/blocks"))
            $names[] = 'templates/blocks';
        if (file_exists(sys::code() . "modules/$moddir/xartemplates/properties"))
            $names[] = 'templates/properties';
        if (file_exists(sys::code() . "modules/$moddir/xartemplates/objects"))
            $names[] = 'templates/objects';
    }
    return $names;
}

?>
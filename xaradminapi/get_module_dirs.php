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
            $names[] = 'xartemplates/includes';
        if (file_exists(sys::code() . "modules/$moddir/xartemplates/blocks"))
            $names[] = 'xartemplates/blocks';
        if (file_exists(sys::code() . "modules/$moddir/xartemplates/properties"))
            $names[] = 'xartemplates/properties';
        if (file_exists(sys::code() . "modules/$moddir/xartemplates/objects"))
            $names[] = 'xartemplates/objects';
    }
    return $names;
}

?>
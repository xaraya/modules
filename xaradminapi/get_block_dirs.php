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
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 *  This function returns an array containing all the php files
 *  in a given directory that start with "xar"
 * @return array
 */
function translations_adminapi_get_block_dirs($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert(isset($blockdir));

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
    if (file_exists(sys::code() . "blocks/$blockdir")) {
        $dd = opendir(sys::code() . "blocks/$blockdir");
        while ($filename = readdir($dd)) {
            if (!is_dir(sys::code() . "blocks/$blockdir/$filename")) continue;
            if (substr($filename,0,3) != "xar") continue;
            if (in_array($filename, $dropit)) continue;
            $names[] = mb_ereg_replace("^xar","",$filename);
        }
        closedir($dd);
    }
    if (file_exists(sys::code() . "blocks/$blockdir/xartemplates")) {
        if (file_exists(sys::code() . "blocks/$blockdir/xartemplates/includes"))
            $names[] = 'templates/includes';
        if (file_exists(sys::code() . "blocks/$blockdir/xartemplates/blocks"))
            $names[] = 'templates/blocks';
        if (file_exists(sys::code() . "blocks/$blockdir/xartemplates/properties"))
            $names[] = 'templates/properties';
        if (file_exists(sys::code() . "blocks/$blockdir/xartemplates/objects"))
            $names[] = 'templates/objects';
    }
    return $names;
}

?>
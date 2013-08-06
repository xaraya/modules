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
            if (!preg_match('!^([a-z\-_]+)\.php$!i', $filename, $matches)) continue;
            $phpname = $matches[1];
            if ($phpname == 'xartables') continue;
            $names[] = mb_ereg_replace("^xar","",$phpname);
        }
        closedir($dd);
    }
    return $names;
}

?>
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

//  This function returns an array containing all the php files
//  in a given property directory

function translations_adminapi_get_property_phpfiles($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert(isset($propertydir));

    $names = array();
    if (file_exists(sys::code() . "properties/$propertydir")) {
        $dd = opendir(sys::code() . "properties/$propertydir");
        while ($filename = readdir($dd)) {
            if (!preg_match('!^([a-z\-_]+)\.php$!i', $filename, $matches)) continue;
            $phpname = $matches[1];
            $names[] = mb_ereg_replace("^xar","",$phpname);
        }
        closedir($dd);
    }
    return $names;
}

?>
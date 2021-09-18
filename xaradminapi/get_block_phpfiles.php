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
//  in a given directory that start with "xar"
function translations_adminapi_get_block_phpfiles($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert('isset($blockdir)');

    $names = [];
    if (file_exists(sys::code() . "blocks/$blockdir")) {
        $dd = opendir(sys::code() . "blocks/$blockdir");
        while ($filename = readdir($dd)) {
            if (!preg_match('!^([a-z\-_]+)\.php$!i', $filename, $matches)) {
                continue;
            }
            $phpname = $matches[1];
            $names[] = mb_ereg_replace("^xar", "", $phpname);
        }
        closedir($dd);
    }
    return $names;
}

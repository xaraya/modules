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

function translations_adminapi_get_block_files($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert(isset($blockdir) && isset($pattern));

    $names = array();
    if (file_exists($blockdir)) {
        $dd = opendir($blockdir);
        while ($filename = readdir($dd)) {
            if (!preg_match($pattern, $filename, $matches)) continue;
            $names[] = $matches[1];
        }
        closedir($dd);
    }
    return $names;
}

?>
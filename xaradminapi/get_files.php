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

function translations_adminapi_get_files($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert(isset($themedir));
    $prefix = "themes/$themedir/";

    $files = array();
    $pattern = '/^([a-z0-9\-_]+)\.xt$/i';
    foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($prefix)) as $file) {
    
        // Ignore directories, files that are not templates, and templates in *ignore directories
        if (pathinfo($file, PATHINFO_EXTENSION) != 'xt' || preg_match('!/(ignore)/!', $file->getPathName())) continue;
                
        // Make sure the file has a good name
        if (!preg_match($pattern, $file->getFileName(), $matches)) continue;
        $files[] = $file->getPathName();
    }
    sort($files);
    return $files;
}

?>
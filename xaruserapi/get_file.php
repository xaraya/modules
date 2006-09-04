<?php
/**
 * Gallery
 *
 * @package   Xaraya eXtensible Management System
 * @copyright (C) 2006 by Brian McGilligan
 * @license   New BSD License <http://www.abrasiontechnology.com/index.php/page/7>
 * @link      http://www.abrasiontechnology.com/
 *
 * @subpackage Gallery module
 * @author     Brian McGilligan
 */

/**
    Gets all the files for a gallery in the db.
*/
function gallery_userapi_get_file($args)
{
    extract($args);

    if( !isset($file_id) )
        return array();

    $files = xarModAPIFunc('gallery', 'user', 'get_files', $args);

    $file = array();
    if( isset($files[$file_id]) )
        $file = $files[$file_id];

    return $file;
}
?>
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
function gallery_adminapi_mkdir_album($args)
{
    extract($args);

    if( empty($name) )
        return false;

    $path = xarModGetVar('gallery', 'file_path');
    if( file_exists($path) && !file_exists($path . $name))
    {
        mkdir("$path$name", 0755);
    }

    return true;
}
?>
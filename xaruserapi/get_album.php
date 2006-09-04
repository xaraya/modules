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

function gallery_userapi_get_album($args)
{
    extract($args);

    if( !isset($album_id) && !isset($name) )
        return array();

    $albums = xarModAPIFunc('gallery', 'user', 'get_albums', $args);

    $album = array();
    if( isset($album_id) && isset($albums[$album_id]) )
        $album = $albums[$album_id];
    elseif( isset($name) )
        $album =@ current($albums);

    return $album;
}
?>
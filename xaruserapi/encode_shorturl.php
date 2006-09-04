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
 * return the path for a short URL to xarModURL for this module
 * @param $args the function and arguments passed to xarModURL
 * @returns string
 * @return path to be added to index.php for a short URL, or empty if failed
 */
function gallery_userapi_encode_shorturl($args)
{
    // Get arguments from argument array
    extract($args);

    if( !empty($album_id) )
    {
        $album = xarModAPIFunc('gallery', 'user', 'get_album',
            array(
                'album_id' => $album_id
            )
        );

    }

    $path = '/gallery/';
    if( $func == 'view' )
    {
        if( isset($album['name']) )
        {
            $path .= "{$album['name']}/";
        }
    }
    elseif( $func == 'display' )
    {
        if( isset($album['name']) )
        {
            $path .= "{$album['name']}/";
        }
        $path .= "$file_id/";
    }
    elseif( $func == 'main' )
    {
        // Need to be able to add cats here
    }
    elseif( $func == 'my_view' )
    {
        $path .= "$func/";
    }
    elseif( $func == 'new_album_wizard' )
    {
        $path .= "$func/";
    }
    elseif( $func == 'new' )
    {
        $path .= "$func/";
        if( isset($album['name']) )
        {
            $path .= "{$album['name']}/";
        }
    }
    else
    {
        return;
    }

    unset($args['func']);
    unset($args['album_id']);
    unset($args['file_id']);

    $join = "?";
    $seperator = "&";
    $extra = array();
    foreach( $args as $key => $arg )
    {
        $extra[] = "$key=$arg";
    }

    if( count($extra) > 0 )
    {
        $path .= $join . join($seperator, $extra);
    }

    return $path;
}
?>

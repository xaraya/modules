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
 * extract function and arguments from short URLs for this module, and pass
 * them back to xarGetRequestInfo()
 * @param $params array containing the elements of PATH_INFO
 * @returns array
 * @return array containing func the function to be called and args the query
 *         string arguments, or empty if it failed
 */
function gallery_userapi_decode_shorturl($params)
{
    $args = array();

    $module = 'gallery';


    // Check if we're dealing with an alias here
    /*
    if ($params[0] != $module) {
        $alias = xarModGetAlias($params[0]);
        if ($module == $alias) {
            // get gallery id
        }
    }
*/

    if( empty($params[1]) )
    {
        return; //array('main', $args);
    }
    elseif( preg_match('/^(\d+)$/',$params[1],$matches) )
    {
        $args['file_id'] = $matches[1];
        return array('display', $args);

    }
    elseif( preg_match('/^my_view/i',$params[1]) )
    {
        return array('my_view', $args);
    }
    elseif( preg_match('/^new_album_wizard/i',$params[1]) )
    {
        return array('new_album_wizard', $args);
    }
    elseif( $params[1] == 'new' ) //preg_match('/^new/i',$params[1]) )
    {
        if( isset($params[2]) )
        {
            $album = xarModAPIFunc('gallery', 'user', 'get_album', array('name' => $params[2]));
            $args['album_id'] = $album['album_id'];
        }
        return array('new', $args);
    }
    elseif( preg_match('/^c(_?[0-9 +-]+)/',$params[1],$matches) )
    {
        $catid = $matches[1];
        $args['catid'] = $catid;
        if( !empty($params[2]) )
        {
            // gallery
        }

        // Decode should return the same array of arguments that was passed to encode
        if( strpos($catid,'+') === FALSE )
        {
            $args['cids'] = explode('-',$catid);
        } else {
            $args['cids'] = explode('+',$catid);
            $args['andcids'] = TRUE;
        }

        return array('main', $args);

    } else {
        // gallery name is first param and we want to extract it.
        $album = xarModAPIFunc('gallery', 'user', 'get_album', array('name' => $params[1]));
        $args['album_id'] = $album['album_id'];
        if( !isset($params[2]) ){ return array('view', $args); }

        if( preg_match('/(\d+)/',$params[2],$matches) )
        {
            $args['file_id'] = $params[2];
            return array('display', $args);
        }
    }

    // default : return nothing -> no short URL
    return;
}
?>
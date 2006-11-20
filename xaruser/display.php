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

function gallery_user_display($args)
{
    if( !xarVarFetch('album_id', 'int', $album_id, null, XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('file_id', 'int', $file_id, null) ){ return false; }
    if( !xarVarFetch('image_size', 'str', $image_size, '') ){ return false; }

    if( !Security::check(SECURITY_READ, 'gallery', FILE_ITEMTYPE, $file_id) ){ return false; }

    // Process params
    xarVarSetCached('modules.security', 'itemtype', FILE_ITEMTYPE);
    xarVarSetCached('modules.security', 'itemid', $file_id);

    if( empty($image_size) ){
        $session_image_size = xarSessionGetVar('modules.gallery.image_size');
        if( !empty($session_image_size) ){
            $image_size = $session_image_size;
        } else {
            $image_size = '600x480';
        }
    }
    xarSessionSetVar('modules.gallery.image_size', $image_size);
    list($width, $height) =@ split('x', $image_size);

    $album = xarModAPIFunc('gallery', 'user', 'get_album',
        array('album_id' => $album_id)
    );

    if( isset($album['settings']['watermark_id']) ){
        $watermark_id = $album['settings']['watermark_id'];
    } else {
        $watermark_id = null;
    }

    $watermark = xarModAPIFunc('gallery', 'user', 'get_watermark',
        array('watermark_id' => $watermark_id)
    );

    $file = xarModAPIFunc('gallery', 'user', 'get_file',
        array(
            'album_id'   => $album_id
            , 'file_id'  => $file_id
            , 'level'    => SECURITY_READ
        )
    );
    if( empty($file) ){ return false; }

    /*
        Set Meta Data for this file
    */
    // Dynamic Description
    if( xarModIsAvailable('metadata') )
    {
        xarModAPIFunc('metadata', 'user', 'set',
            array('type' => 'keywords', 'value' => "{$file['name']} {$file['summary']}")
        );
        xarModAPIFunc('metadata', 'user', 'set',
            array('type' => 'description', 'value' => $file['summary'])
        );
    }

    /*
        Gets the previous and next files
    */
    $sort = null;
    if( isset($album['settings']['sort_order']) ){
        $sort = $album['settings']['sort_order'];
    } else {
        $sort = xarModGetVar('gallery', 'sort');
    }
    $prev = xarModAPIFunc('gallery', 'user', 'get_previous',
        array(
            'file_id'  => $file_id,
            'album_id' => $album_id,
            'file'     => $file,
            'sort'     => $sort,
            'level'    => SECURITY_READ,
            'states'   => array('APPROVED')
        )
    );
    $next = xarModAPIFunc('gallery', 'user', 'get_next',
        array(
            'file_id'  => $file_id,
            'album_id' => $album_id,
            'file'     => $file,
            'sort'     => $sort,
            'level'    => SECURITY_READ,
            'states'   => array('APPROVED')
        )
    );

    $data = array();

    /*
        Call Display hooks
    */
    $hooks = xarModCallHooks('item', 'display', $file_id,
        array(
            'module'    => 'gallery',
            'itemtype'  => FILE_ITEMTYPE,
            'itemid'    => $file_id,
            'returnurl' => xarModURL('gallery', 'user', 'display',
                array(
                    'album_id' => $album_id,
                    'file_id' => $file_id
                )
            )
        ),
        'gallery'
    );
    if( empty($hooks) ) {
        $data['hooks'] = array();
    } else {
        $data['hooks'] = $hooks;
    }

    if( !empty($prev) )
    {
        $data['prev'] = $prev;
        $data['prev_link'] = xarModURL('gallery', 'user', 'display',
            array(
                'album_id' => $album_id,
                'file_id'  => $prev['file_id']
            )
        );
    }
    if( !empty($next) )
    {
        $data['next'] = $next;
        $data['next_link'] = xarModURL('gallery', 'user', 'display',
            array(
                'album_id' => $album_id,
                'file_id'  => $next['file_id']
            )
        );
    }

    $data['is_album_admin'] = Security::check(SECURITY_ADMIN, 'gallery', ALBUM_ITEMTYPE, $album_id, false);

    // Set Page title
    xarTplSetPageTitle($file['name']);

    if( !empty($watermark) )
        $data['watermark'] = $watermark['main'];
    $data['album'] = $album;
    $data['files_path'] = xarModGetVar('gallery', 'file_path');
    $data['file'] = $file;
    $data['back_link'] = xarModURL('gallery', 'user', 'view',
        array(
            'album_id' => $album_id
        )
    );

    $data['width'] = $width;
    $data['height'] = $height;
    $data['image_size'] = $image_size;

    if( xarRequestGetVar('theme') == 'print' )
    {
        return xarTplModule('gallery', 'user', 'display', $data, 'standalone');
    }

    $data['sizes'] = array('600x480', '800x600', '1024x768');


    return xarTplModule('gallery', 'user', 'display', $data);
}
?>
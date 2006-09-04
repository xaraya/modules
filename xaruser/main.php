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
/*
    Display all the galleries
*/
function gallery_user_main()
{
    if( !Security::check(SECURITY_OVERVIEW, 'gallery') ){ return false; }

    if( !xarVarFetch('catid', 'int', $catid, null, XARVAR_NOT_REQUIRED) ){ return false; }

    $albums = xarModAPIFunc('gallery', 'user', 'get_albums',
        array(
            'cids'              => !empty($catid) ? array($catid) : null,
            'states'            => array('APPROVED'),
            'hide_empty_albums' => true,
            'level'             => SECURITY_OVERVIEW,
            'sort'              => xarModGetVar('gallery', 'sort') . ' ' . xarModGetVar('gallery', 'sort_order')
        )
    );

    foreach( $albums as $key => $album )
    {
        $albums[$key]['link'] = xarModURL('gallery', 'user', 'view',
            array(
                'album_id' => $album['album_id']
            )
        );
    }

    $data = array();

    $data['albums'] = $albums;
    $data['files_path'] = xarModGetVar('gallery', 'file_path');

    return $data;
}
?>
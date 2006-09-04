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
    Designed to be navigation for users
*/

include_once('modules/gallery/xarclass/Panel.php');

function gallery_admin_panel($args)
{
    if( !class_exists('Panel') ){ return ''; }

    if( !xarVarFetch('album_id', 'int', $album_id, null, XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('file_id', 'int', $file_id, null, XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('what',     'str', $what, 'albums', XARVAR_NOT_REQUIRED) ){ return false; }

    extract($args);

    $panel = new Panel('Actions');

    if( $what == 'albums' )
    {
        $url = xarModURL('gallery', 'admin', 'new',
            array(
                'what' => 'albums'
            )
        );
        $panel->add('new', 'New Album', $url, false);

        $url = xarModURL('gallery', 'admin', 'view',
            array(
                'what' => 'files'
            )
        );
        $panel->add('new', 'View All Photos', $url, false);

        $url = xarModURL('gallery', 'admin', 'view',
            array(
                'what' => 'watermarks'
            )
        );
        $panel->add('new', 'View Watermarks', $url, false);

    }
    else if( $what == 'files' )
    {
        $url = xarModURL('gallery', 'admin', 'new',
            array(
                'what' => 'files',
                'album_id' => $album_id
            )
        );
        $panel->add('new', 'New Photo', $url, false);

        $url = xarModURL('gallery', 'admin', 'view',
            array(
                'what' => 'albums'
            )
        );
        $panel->add('new', 'View Albums', $url, false);

    }
    else if( $what == 'watermarks' )
    {
        $url = xarModURL('gallery', 'admin', 'new',
            array(
                'what' => 'watermarks'
            )
        );
        $panel->add('new', 'New Watermark', $url, false);

    }

    return $panel->output();
}
?>
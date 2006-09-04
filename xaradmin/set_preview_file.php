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
function gallery_admin_set_preview_file($args)
{
    if( !xarVarFetch('album_id', 'int', $album_id) ){ return false; }
    if( !xarVarFetch('file_id', 'int', $file_id) ){ return false; }
    if( !xarVarFetch('returnurl', 'str', $returnurl) ){ return false; }

    extract($args);

    if( !Security::check(SECURITY_ADMIN, 'gallery', ALBUM_ITEMTYPE, $album_id) ){ return false; }

    $result = xarModAPIFunc( 'gallery', 'admin', 'update_album_settings',
        array(
            'album_id' => $album_id,
            'preview_file' => $file_id
        )
    );

    xarResponseRedirect($returnurl);
    return false;
}
?>
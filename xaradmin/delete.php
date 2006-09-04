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

function gallery_admin_delete($args)
{
    extract($args);

    if( !xarVarFetch('what', 'str', $what, 'albums', XARVAR_NOT_REQUIRED) ){ return false; }
    $itemtype = 0;
    if( $what == 'albums' ){ $itemtype = ALBUM_ITEMTYPE; }
    elseif( $what == 'files' ){ $itemtype = FILE_ITEMTYPE; }

    if( !Security::check(SECURITY_ADMIN, 'gallery', $itemtype) ){ return false; }

    switch( $what )
    {
        case 'albums':
            if( !xarVarFetch('album_id', 'int', $album_id, null) ){ return false; }

            xarModAPIFunc('gallery', 'admin', 'delete_album',
                array(
                    'album_id' => $album_id
                )
            );

            xarResponseRedirect(xarModURL('gallery', 'admin', 'view'));
            return false;
            break;

        case 'files':
            if( !xarVarFetch('file_id', 'int', $file_id, null) ){ return false; }
            if( !xarVarFetch('confirm', 'str', $confirm, null, XARVAR_NOT_REQUIRED) ){ return false; }
            if( !xarVarFetch('album_id', 'int', $album_id, null, XARVAR_NOT_REQUIRED) ){ return false; }

            if( !is_null($confirm) )
            {
                if( !xarSecConfirmAuthKey() ){ return false; }

                $result = xarModAPIFunc('gallery', 'admin', 'delete_file',
                    array('file_id' => $file_id)
                );

                if( $result === true )
                {
                    // success
                    $message = "File was successfully deleted.";
                    xarSessionSetVar('statusmsg', $message);
                }
                else
                {
                    // failed
                    $message = "There was a problem deleting the file.";
                    xarSessionSetVar('statusmsg', $message);
                }

                if( !xarVarFetch(
                    'returnurl',
                    'str',
                    $returnurl,
                    xarModURL('gallery', 'admin', 'view', array('what' => 'files', 'album_id' => $album_id)),
                    XARVAR_NOT_REQUIRED
                    )
                ){ return false; }
                xarResponseRedirect($returnurl);
                return false;
            }

            $data = array();
            $data['album_id'] = $album_id;
            $data['file'] = xarModAPIFunc('gallery', 'user', 'get_file',
                array('file_id' => $file_id)
            );
            $data['files_path'] = xarModGetVar('gallery', 'file_path');
            $data['returnurl'] = xarServerGetVar('HTTP_REFERER');
            return xarTplModule('gallery', 'admin', 'delete', $data, 'file');
            break;

        case 'watermarks':
            if( !xarVarFetch('watermark_id', 'int', $watermark_id, null) ){ return false; }

            $all_watermarks =@ unserialize(xarModGetVar('gallery', 'watermarks'));
            unset($all_watermarks[$watermark_id]);
            xarModSetVar('gallery', 'watermarks', serialize($all_watermarks));

            xarResponseRedirect(xarModURL('gallery', 'admin', 'view', array('what' => 'watermarks')));
            return false;

            break;

        default:
    }

    //xarModAPIFunc();

    return true;
}
?>
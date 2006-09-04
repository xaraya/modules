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

function gallery_user_delete($args)
{
    //if( !Security::check(SECURITY_ADMIN, 'gallery') ){ return false; }

    extract($args);

    if( !xarVarFetch('what', 'str', $what, 'albums', XARVAR_NOT_REQUIRED) ){ return false; }

    switch( $what )
    {
        case 'albums':
            if( !xarVarFetch('album_id', 'int', $album_id, null) ){ return false; }

//            xarModAPIFunc('gallery', 'admin', 'delete_gallery',
//                array(
//                    'gallery_id' => $gallery_id
//                )
//            );

            xarResponseRedirect(xarModURL('gallery', 'user', 'my_view'));
            return false;
            break;

        case 'files':
            if( !xarVarFetch('file_id', 'int', $file_id, null) ){ return false; }
            if( !xarVarFetch('confirm', 'str', $confirm, null, XARVAR_NOT_REQUIRED) ){ return false; }

            if( !Security::check(SECURITY_WRITE, 'gallery', FILE_ITEMTYPE, $file_id) ){ return false; }

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
                    xarModURL('gallery', 'user', 'my_view'),
                    XARVAR_NOT_REQUIRED
                    )
                ){ return false; }
                xarResponseRedirect($returnurl);
                return false;
            }

            $data = array();
            $data['file'] = xarModAPIFunc('gallery', 'user', 'get_file',
                array('file_id' => $file_id)
            );
            $data['files_path'] = xarModGetVar('gallery', 'file_path');
            $data['returnurl'] = xarServerGetVar('HTTP_REFERER');
            return xarTplModule('gallery', 'user', 'delete', $data, 'file');
            break;

        default:
    }

    return true;
}
?>
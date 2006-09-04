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
    Create new gallerys/files
*/
function gallery_user_new($args)
{

    if( !xarVarFetch('numitems', 'int:1:10', $numitems, 5, XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('what', 'str', $what, 'files', XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('submit', 'str', $submit, null, XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('album_id', 'int', $album_id, null, XARVAR_NOT_REQUIRED) ){ return false; }
    extract($args);

    $data = array();

    switch( $what )
    {
        case 'files':
            if( !Security::check(SECURITY_WRITE, 'gallery', FILE_ITEMTYPE) ){ return false; }

            $itemtype = FILE_ITEMTYPE;
            if( !xarVarFetch('numitems', 'int', $numitems, 1, XARVAR_NOT_REQUIRED) ){ return false; }

            if( !empty($submit) )
            {
                //set_time_limit(6000);
                //ini_set('upload_max_filesize', '8M');
                //ini_set('post_max_size', '8M');
                if( !xarSecConfirmAuthKey() ){ return false; }

                if( !xarVarFetch('name', 'str', $name, '', XARVAR_NOT_REQUIRED) ){ return false; }
                if( !xarVarFetch('summary', 'str', $summary, '', XARVAR_NOT_REQUIRED) ){ return false; }
                if( !xarVarFetch('links', 'array', $links, array()) ){ return false; }

                if( empty($links) )
                {
                    /*
                        throw or handle exception
                    */
                    return false;
                }

                /*
                     Create the DB entries and linkage
                */
                $file_id = xarModAPIFunc('gallery', 'admin', 'create_file',
                    array(
                        'name' => 'tmp',
                        'summary' => $summary,
                        'status' => 'SUBMITTED'
                    )
                );

                if( !empty($_FILES['file']) )
                {
                    $file_info = xarModAPIFunc('gallery', 'admin', 'process_file',
                        array(
                            'file_id' => $file_id,
                            'name' => 'file'
                        )
                    );
                }

                if( empty($file_info) )
                {
                    /*
                        throw an exception
                    */
                    return false;
                }

                // Make sure name is set
                if( empty($name) )
                {
                    $name = $file_info['name'];
                }

                if( !empty($file_id) )
                {
                    /*
                         update the DB entries and linkage
                    */
                    xarModAPIFunc('gallery', 'admin', 'update_file',
                        array(
                            'file_id' => $file_id,
                            'name' => $name,
                            'file' => $file_info['file'],
                            'file_type' => $file_info['type'],
                            'file_size' => $file_info['size']
                        )
                    );

                    xarModAPIFunc('gallery', 'admin', 'link_files',
                        array(
                            'file_ids' => array($file_id)
                            , 'album_ids' => array_keys($links)
                        )
                    );
                    xarModCallHooks('item', 'create', $file_id,
                        array(
                            'module'   => 'gallery',
                            'itemtype' => $itemtype,
                            'itemid' => $file_id
                        )
                    );
                }

                if( !empty($gallery_id) )
                {
                    $url = xarModURL('gallery', 'user', 'view',
                        array(
                            'what' => $what,
                            'album_id' => $album_id
                        )
                    );
                }
                else
                {
                    $url = xarModURL('gallery', 'user', 'main');
                }

                // Look like it was a success.
                xarSessionSetVar('statusmsg', xarModGetVar('gallery', 'new_file_success'));

                xarResponseRedirect($url);
                return false;
            }

            /*
                Set file defaults for creation
            */
            $data['numitems'] = $numitems;
            $data['file'] = array(
                'name' => '',
                'summary' => '',
                'file' => ''
            );

            xarModAPILoad('security');
            $data['albums'] = xarModAPIFunc('gallery', 'user', 'get_albums',
                array(
                    'level' => SECURITY_COMMENT,
                    'states' => array('APPROVED')
                )
            );

            break;
        default:
            exit();
    }

    /*
        Common template code
    */
    $data['what'] = $what;
    $data['album_id'] = $album_id;
    $data['submit_text'] = 'Create';

    $data['authid'] = xarSecGenAuthKey();

    $hooks = xarModCallHooks('item', 'new', '',
        array(
            'module'   => 'gallery',
            'itemtype' => $itemtype
        )
    );
    if( empty($hooks) ) {
        $data['hooks'] = array();
    } else {
        $data['hooks'] = $hooks;
    }

    return xarTplModule('gallery', 'user', 'new', $data, $what);
}
?>
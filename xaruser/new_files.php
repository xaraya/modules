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
function gallery_user_new_files($args)
{
    if( !Security::check(SECURITY_WRITE, 'gallery', FILE_ITEMTYPE) ){ return false; }

    if( !xarVarFetch('numitems', 'int:1:10', $numitems, 5, XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('submit', 'str', $submit, null, XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('album_id', 'int', $album_id, null, XARVAR_NOT_REQUIRED) ){ return false; }

    extract($args);

    $data = array();

    $itemtype = FILE_ITEMTYPE;

    if( !empty($submit) )
    {
        //set_time_limit(6000);
        //ini_set('upload_max_filesize', '8M');
        //ini_set('post_max_size', '8M');
        if( !xarSecConfirmAuthKey() ){ return false; }

        if( !xarVarFetch('name', 'str', $name, '', XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('summary', 'str', $summary, '', XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('links', 'array', $links, array()) ){ return false; }
        if( !xarVarFetch('title', 'array', $titles, array(), XARVAR_NOT_REQUIRED) ){ return false; }

        /*
            Start processing uploaded files
            File indexes start at 1 here.
        */
        for( $i = 1; $i < $numitems+1; $i++ )
        {
            if( !empty($_FILES['file']['name'][$i]) )
            {
                $file_id = xarModAPIFunc('gallery', 'admin', 'create_file',
                    array(
                        'name' => 'tmp',
                        'file' => '',
                        'status' => 'SUBMITTED'
                    )
                );


                $file_info = xarModAPIFunc('gallery', 'admin', 'process_file',
                    array(
                        'file_id' => $file_id,
                        'name' => 'file',
//                            'base_dir' => $params['name'],
                        'index' => $i
                    )
                );

                if( !empty($file_info) )
                {
                    /*
                         update the DB entries with storage info and linkage
                    */
                    if( !empty($titles[$i]) ){ $file_info['name'] = $titles[$i]; }
                    xarModAPIFunc('gallery', 'admin', 'update_file',
                        array(
                            'file_id' => $file_id,
                            'name' => $file_info['name'],
                            'file' => $file_info['file'],
                            'file_type' => $file_info['type'],
                            'file_size' => $file_info['size']
                        )
                    );

                    if( !empty($file_id) )
                    {
                        xarModAPIFunc('gallery', 'admin', 'link_files',
                            array(
                                'file_ids' => array($file_id),
                                'album_ids' => array_keys($links)
                            )
                        );
                        xarModCallHooks('item', 'create', $file_id,
                            array(
                                'module'   => 'gallery',
                                'itemtype' => FILE_ITEMTYPE,
                                'itemid'  => $file_id
                            )
                        );
                    }
                }
            }
        }

        if( !empty($album_id) )
        {
            $url = xarModURL('gallery', 'user', 'view',
                array(
                    //'what' => $what,
                    'album_id' => $album_id
                )
            );
        }
        else
        {
            $url = xarModURL('gallery', 'user', 'main');
        }

        // Look like it was a success.
        xarSessionSetVar('status_message', xarModGetVar('gallery', 'new_file_success'));

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

    $data['albums'] = xarModAPIFunc('gallery', 'user', 'get_albums',
        array(
            'level' => SECURITY_COMMENT,
            'states' => array('APPROVED')
        )
    );

    /*
        Common template code
    */
    $data['album_id'] = $album_id;
    $data['submit_text'] = 'Create';
    $data['returnurl'] = xarRequestGetVar('HTTP_HOST');
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

    return xarTplModule('gallery', 'user', 'new_files', $data);
}
?>
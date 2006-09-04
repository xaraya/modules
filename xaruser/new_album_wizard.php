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

function gallery_user_new_album_wizard($args)
{
    if( !Security::check(SECURITY_WRITE, 'gallery', ALBUM_ITEMTYPE) ){ return false; }

    if( !xarVarFetch('numitems', 'int:1:10', $numitems, 5, XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('submit', 'str', $submit, null, XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('catid', 'str', $catid, null, XARVAR_NOT_REQUIRED) ){ return false; }

    if( !is_null($submit) )
    {
        if( !xarSecConfirmAuthKey() ){ return false; }

        if( !xarVarFetch('album_name', 'pre:lower:ftoken:passthru:str:1:', $params['name']) ){ return false; }
        if( !xarVarFetch('album_name', 'str:1', $params['display_name'], '') ){ return false; }
        if( !xarVarFetch('desc', 'str:1', $params['description'], '', XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('title', 'array', $titles, array(), XARVAR_NOT_REQUIRED) ){ return false; }

        /*
            First make sure the name if unique
        */
        $params['name'] = xarModAPIFunc('gallery', 'user', 'make_unique_album_name',
            array(
                'name' => $params['name']
            )
        );
        $params['status'] = 'SUBMITTED';
        $album_id = xarModAPIFunc( 'gallery', 'admin', 'create_album', $params );

        if( $album_id > 0 )
        {
            $settings = xarModAPIFunc('gallery', 'user', 'get_default_album_settings');
            $settings['album_id'] = $album_id;
            $result = xarModAPIFunc(
                'gallery', 'admin', 'update_album_settings', $settings
            );

            xarModCallHooks('item', 'create', $album_id,
                array(
                    'module'   => 'gallery',
                    'itemtype' => ALBUM_ITEMTYPE,
                    'itemid' => $album_id
                )
            );

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
                                    'album_ids' => array($album_id)
                                )
                            );
                            xarModCallHooks('item', 'create', $file_id,
                                array(
                                    'module'   => 'gallery',
                                    'itemtype' => FILE_ITEMTYPE,
                                    'itemid' => $file_id
                                )
                            );
                        }
                    }
                }
            }

            // Look like it was a success.
            xarSessionSetVar('statusmsg', xarModGetVar('gallery', 'new_album_success'));

            /*
                Do Redirect
            */
            xarResponseRedirect(xarModURL('gallery', 'user', 'view', array('album_id' => $album_id)));
            return false;
        }
        else
        {
            /*
                Exception Problem creating gallery
            */
            $msg = "The Gallery was not able to be created!";

            //xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA' , $msg);
            return false;
        }
    }


    $data = array();

    $data['numitems'] = $numitems;
    $data['submit_text'] = 'Create';
    $data['authid'] = xarSecGenAuthKey();

    $_GET['itemtype'] = ALBUM_ITEMTYPE;
    $hooks = xarModCallHooks('item', 'new', '',
        array(
            'module'   => 'gallery',
            'itemtype' => ALBUM_ITEMTYPE,
            'cids' => array($catid)
        )
    );
    if( empty($hooks) ) {
        $data['hooks'] = array();
    } else {
        $data['hooks'] = $hooks;
    }


    return xarTplModule('gallery', 'user', 'new_album_wizard', $data);
}
?>
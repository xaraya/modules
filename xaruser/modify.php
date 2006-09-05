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
    Modify
*/
function gallery_user_modify($args)
{
    if( !xarVarFetch('what', 'str', $what, 'files', XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('submit', 'str', $submit, null, XARVAR_NOT_REQUIRED) ){ return false; }

    $data = array();

    switch( $what )
    {
        case 'albums':
            if( !xarVarFetch('album_id', 'int', $album_id, null, XARVAR_NOT_REQUIRED) ){ return false; }
            $itemtype = ALBUM_ITEMTYPE;
            $itemid = $album_id;
            if( !Security::check(SECURITY_WRITE, 'gallery', $itemtype, $itemid) ){ return false; }

            /*
                Update the gallery if form is submitted
            */
            if( !empty($submit) )
            {
                if( !xarSecConfirmAuthKey() ){ return false; }

                if( !xarVarFetch('name', 'pre:lower:ftoken:passthru:str:1:', $name, '') ){ return false; }
                if( !xarVarFetch('display_name', 'str:1', $params['display_name'], '') ){ return false; }
                if( !xarVarFetch('desc', 'str', $params['description'], '', XARVAR_NOT_REQUIRED) ){ return false; }

                if( !xarVarFetch('settings_show_date', 'str', $settings_params['show_date'], '', XARVAR_NOT_REQUIRED) ){ return false; }
                if( !xarVarFetch('settings_items_per_page', 'int', $settings_params['items_per_page'], 10, XARVAR_NOT_REQUIRED) ){ return false; }
                if( !xarVarFetch('settings_cols_per_page', 'int', $settings_params['cols_per_page'], 3, XARVAR_NOT_REQUIRED) ){ return false; }
                if( !xarVarFetch('settings_file_width', 'str', $settings_params['file_width'], '150px', XARVAR_NOT_REQUIRED) ){ return false; }
                if( !xarVarFetch('settings_file_quality', 'int:1:100', $settings_params['file_quality'], '100', XARVAR_NOT_REQUIRED) ){ return false; }

                $params['album_id'] = $album_id;
                $result = xarModAPIFunc( 'gallery', 'admin', 'update_album', $params );
                $settings_params['album_id'] = $album_id;
                $result = xarModAPIFunc( 'gallery', 'admin', 'update_album_settings', $settings_params );

                xarModCallHooks('item', 'update', $itemid,
                    array(
                        'module'   => 'gallery',
                        'itemtype' => $itemtype,
                        'itemid' => $itemid
                    )
                );

                xarResponseRedirect(xarModURL('gallery', 'user', 'view', array('what' => $what)));
                return false;
            }

            /*
                Get all the gallerys
            */
            $album =& xarModAPIFunc('gallery', 'user', 'get_album',
                array(
                    'album_id' => $album_id
                )
            );

            if( !empty($gallery['settings']['show_date']) )
            { $gallery['settings']['show_date'] = 'checked="checked"'; }
            else { $gallery['settings']['show_date'] = ''; }

            $data['album_id'] = $album_id;
            $data['album'] =& $album;

            break;

        /*
            Start the files processing
        */
        case 'files':

            if( !xarVarFetch('file_id', 'int', $file_id, null, XARVAR_NOT_REQUIRED) ){ return false; }
            if( !xarVarFetch('redirecturl', 'str', $redirecturl, null, XARVAR_NOT_REQUIRED) ){ return false; }
            if( !xarVarFetch('returnurl', 'str', $returnurl, null, XARVAR_NOT_REQUIRED) ){ return false; }
            $itemtype = FILE_ITEMTYPE;
            $itemid = $file_id;
            if( !Security::check(SECURITY_WRITE, 'gallery', $itemtype, $itemid) ){ return false; }

            /*
                Update the gallery if form is submitted
            */
            if( !empty($submit) )
            {
                if( !xarSecConfirmAuthKey() ){ return false; }

                if( !xarVarFetch('name', 'str:1', $name, '', XARVAR_NOT_REQUIRED) ){ return false; }
                if( !xarVarFetch('summary', 'str', $summary, '', XARVAR_NOT_REQUIRED) ){ return false; }
                if( !xarVarFetch('links', 'array', $links, array(), XARVAR_NOT_REQUIRED) ){ return false; }

                if( !empty($_FILES['file']) )
                {
                    $file_info = xarModAPIFunc('gallery', 'admin', 'process_file',
                        array(
                            'name' => 'file',
                            'file_id' => $file_id,
                            'overwrite' => true
                        )
                    );
                }

                $params =  array(
                    'file_id' => $file_id,
                    'name' => $name,
                    'summary' => $summary,
                    'modified' => time()
                );

                if( !empty($file_info) )
                {
                    //$params['name'] = $file_info['name'];
                    $params['file'] = $file_info['file'];
                    $params['file_size'] = $file_info['size'];
                    $params['file_type'] = $file_info['type'];

                    // If user is not admin change status of file when a
                    // new one is uploaded
                    if( Security::check(SECURITY_ADMIN, 'gallery', FILE_ITEMTYPE, $file_id, false)
                        or Security::check(SECURITY_ADMIN, 'gallery', FILE_ITEMTYPE, 0, false) )
                    {
                        $params['status'] = 'SUBMITTED';
                    }
                }

                xarModAPIFunc('gallery', 'admin', 'update_file', $params);

                xarModAPIFunc('gallery', 'admin', 'link_files',
                    array(
                        'file_ids' => array($file_id),
                        'album_ids' => array_keys($links)
                    )
                );

                xarModCallHooks('item', 'update', $itemid,
                    array(
                        'module'   => 'gallery',
                        'itemtype' => $itemtype,
                        'itemid' => $itemid
                    )
                );

                if( !empty($redirecturl) )
                    xarResponseRedirect($redirecturl);
                elseif( !empty($returnurl) )
                    xarResponseRedirect($returnurl);
                else
                    xarResponseRedirect(xarModURL('gallery', 'user', 'my_view'));
                return false;
            }

            /*
                Get the file
            */
            $file =& xarModAPIFunc('gallery', 'user', 'get_file',
                array(
                    'file_id' => $file_id
                )
            );

            $data['albums'] =& xarModAPIFunc('gallery', 'user', 'get_albums');

            $data['redirecturl'] = $redirecturl;
            $data['file_id'] = $file_id;
            $data['file'] =& $file;

            break;

        default:
            return false;
    }

    /*
        Common template code
    */
    $data['submit_text'] = 'Modify';
    $data['authid'] = xarSecGenAuthKey();

    $hooks = xarModCallHooks('item', 'modify', $itemid,
        array(
            'module'   => 'gallery',
            'itemtype' => $itemtype,
            'itemid' => $itemid
        )
    );
    if( empty($hooks) ) {
        $data['hooks'] = array();
    } else {
        $data['hooks'] = $hooks;
    }


    return xarTplModule('gallery', 'user', 'modify', $data, $what);
}
?>
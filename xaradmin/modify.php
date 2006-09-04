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
function gallery_admin_modify($args)
{
    if( !xarVarFetch('what', 'str', $what, 'albums', XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('modify', 'str', $submit, null, XARVAR_NOT_REQUIRED) ){ return false; }

    $itemtype = 0;
    if( $what == 'albums' ){ $itemtype = ALBUM_ITEMTYPE; }
    elseif( $what == 'files' ){ $itemtype = FILE_ITEMTYPE; }
    if( !Security::check(SECURITY_ADMIN, 'gallery', $itemtype) ){ return false; }

    $data = array();

    switch( $what )
    {
        case 'albums':
            if( !xarVarFetch('album_id', 'int', $album_id, null, XARVAR_NOT_REQUIRED) ){ return false; }
            $itemtype = 1;
            $itemid = $album_id;

            /*
                Update the gallery if form is submitted
            */
            if( !empty($submit) )
            {
                if( !xarVarFetch('name', 'pre:lower:ftoken:passthru:str:1:', $name, '') ){ return false; }
                if( !xarVarFetch('display_name', 'str:1', $params['display_name'], '') ){ return false; }
                if( !xarVarFetch('desc', 'str', $params['description'], '', XARVAR_NOT_REQUIRED) ){ return false; }

                if( !xarVarFetch('settings_sort_order', 'str', $sort_order, 'file_id', XARVAR_NOT_REQUIRED) ){ return false; }
                if( !xarVarFetch('settings_sort_type', 'str', $sort_type, 'ASC', XARVAR_NOT_REQUIRED) ){ return false; }
                if( !xarVarFetch('settings_watermark', 'int', $settings_params['watermark_id'], 0, XARVAR_NOT_REQUIRED) ){ return false; }
                if( !xarVarFetch('settings_show_date', 'str', $settings_params['show_date'], '', XARVAR_NOT_REQUIRED) ){ return false; }
                if( !xarVarFetch('settings_items_per_page', 'int', $settings_params['items_per_page'], 10, XARVAR_NOT_REQUIRED) ){ return false; }
                if( !xarVarFetch('settings_cols_per_page', 'int', $settings_params['cols_per_page'], 3, XARVAR_NOT_REQUIRED) ){ return false; }
                if( !xarVarFetch('settings_file_width', 'str', $settings_params['file_width'], '150px', XARVAR_NOT_REQUIRED) ){ return false; }
                if( !xarVarFetch('settings_file_quality', 'int:1:100', $settings_params['file_quality'], '100', XARVAR_NOT_REQUIRED) ){ return false; }

                $params['album_id'] = $album_id;
                $result = xarModAPIFunc( 'gallery', 'admin', 'update_album', $params );

                $settings_params['album_id'] = $album_id;
                $settings_params['sort_order'] = "$sort_order|$sort_type";
                $result = xarModAPIFunc( 'gallery', 'admin', 'update_album_settings', $settings_params );

                xarModCallHooks('item', 'update', $itemid,
                    array(
                        'module'   => 'gallery',
                        'itemtype' => $itemtype,
                        'itemid' => $itemid
                    )
                );

                xarResponseRedirect(xarModURL('gallery', 'admin', 'view', array('what' => $what)));
                return false;
            }

            /*
                Get all the galleries
            */
            $album = xarModAPIFunc('gallery', 'user', 'get_album',
                array(
                    'album_id' => $album_id
                )
            );

            $data['watermarks'] = xarModAPIFunc('gallery', 'user', 'get_watermarks');

            if( !empty($album['settings']['show_date']) )
            { $album['settings']['show_date'] = 'checked="checked"'; }
            else { $album['settings']['show_date'] = ''; }

            $data['album_id'] = $album_id;
            $data['album'] = $album;

            $data['sort_orders'] =& xarModAPIFunc('gallery', 'user', 'get_sort_orders');
            $data['sort_types'] = array('ASC' => 'Ascending', 'DESC' => 'Descending');

            break;

        case 'files':
            if( !xarVarFetch('file_id', 'int', $file_id, null, XARVAR_NOT_REQUIRED) ){ return false; }
            if( !xarVarFetch('album_id', 'int', $album_id, null, XARVAR_NOT_REQUIRED) ){ return false; }
            $itemtype = 2;
            $itemid = $file_id;

            /*
                Update the gallery if form is submitted
            */
            if( !empty($submit) )
            {
                if( !xarVarFetch('name', 'str:1', $name, '', XARVAR_NOT_REQUIRED) ){ return false; }
                if( !xarVarFetch('summary', 'str', $summary, '', XARVAR_NOT_REQUIRED) ){ return false; }
                if( !xarVarFetch('state', 'str', $state, 'SUBMITTED', XARVAR_NOT_REQUIRED) ){ return false; }
                if( !xarVarFetch('links', 'array', $links) ){ return false; }

                if( empty($links) )
                {
                    $msg = "The file must be in atleast one gallery";
                    //xarSetError();
                    return false;
                }

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
                    'modified' => time(),
                    'status' => $state
                );

                if( !empty($file_info) )
                {
                    //$params['name'] = $file_info['name'];
                    $params['file'] = $file_info['file'];
                    $params['file_size'] = $file_info['size'];
                    $params['file_type'] = $file_info['type'];
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

                xarResponseRedirect(xarModURL('gallery', 'admin', 'view',
                    array(
                        'what' => $what,
                        'album_id' => $album_id
                    )
                ));
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

            $data['states'] =& xarModAPIFunc('gallery', 'user', 'get_States');

            $data['album_id'] = $album_id;
            $data['file_id'] = $file_id;
            $data['file'] =& $file;

            break;

        case 'watermarks':

            if( !xarVarFetch('watermark_id', 'int', $watermark_id, null) ){ return false; }
            $itemtype = 3;
            $itemid = $watermark_id;

            if( !xarVarFetch('reload', 'str', $action, 'modify', XARVAR_NOT_REQUIRED) ){ return false; }

            $default = array(
                'type'      => 'image'
            );
            if( !xarVarFetch('name', 'str', $name, '', XARVAR_NOT_REQUIRED) ){ return false; }
            if( !xarVarFetch('main', 'array', $main, $default, XARVAR_NOT_REQUIRED) ){ return false; }
            if( !xarVarFetch('thumbnail', 'array', $thumbnail, $default, XARVAR_NOT_REQUIRED) ){ return false; }

            if( !empty($submit) and $action == 'modify' )
            {
                /*
                    action must be equal to create to do anthing as we also reload this page to change
                    water mark types
                */
                $all_watermarks =@ unserialize(xarModGetVar('gallery', 'watermarks'));

                if( !is_array($all_watermarks) )
                {
                    return false;
                }
                $watermark = array(
                    'name' => $name,
                    'main' => $main,
                    'thumbnail' => $thumbnail
                );

                $all_watermarks[$watermark_id] = $watermark;

                xarModSetVar('gallery', 'watermarks', serialize($all_watermarks));

                xarResponseRedirect(xarModURL('gallery', 'admin', 'view', array('what' => 'watermarks')));
                return false;
            }
            else if( $action == 'reload' )
            {
                /*
                    If the type of the watermark changes make sure that it the watermark is init with the
                    proper variables.
                */
                $image = array(
                    'type'      => 'image',
                    'filename'  => '',
                    'alignment' => '',
                    'opacity'   => '',
                    'margin'    => ''
                );
                $text = array(
                    'type'      => 'text',
                    'text'      => '',
                    'size'      => '',
                    'alignment' => '',
                    'hex_color' => '',
                    'ttf_font'   => '',
                    'opacity'   => '',
                    'margin'    => '',
                    'angle'     => ''
                );
                if( $main['type'] == 'image' and count($main) != count($image) )
                    $main = $image;
                else if( $main['type'] == 'text' and count($main) != count($text) )
                    $main = $text;
                if( $thumbnail['type'] == 'image' and count($thumbnail) != count($image) )
                    $thumbnail = $image;
                else if( $thumbnail['type'] == 'text' and count($thumbnail) != count($text) )
                    $thumbnail = $text;
            }
            else
            {
                $all_watermarks =@ unserialize(xarModGetVar('gallery', 'watermarks'));
                //list($name, $main, $thumbnail) =@ $all_watermarks[$watermark_id];
                $name =@ $all_watermarks[$watermark_id]['name'];
                $main =@ $all_watermarks[$watermark_id]['main'];
                $thumbnail =@ $all_watermarks[$watermark_id]['thumbnail'];
            }

            $data['types'] = array(
                'image' => 'Image',
                'text'  => 'Text'
            );

            $data['watermark_id'] = $watermark_id;
            $data['name'] = $name;
            $data['main'] = $main;
            $data['thumbnail'] = $thumbnail;

            break;

        default:
            $msg = 'This is a big deal and should never happen!';
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'Missing Var', $msg);
            return false;
    }

    /*
        Common template code
    */
    $data['what'] = $what;
    $data['submit_text'] = 'Modify';

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


    return xarTplModule('gallery', 'admin', 'modify', $data, $what);
}
?>
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
    Create new galleries/files
*/
function gallery_admin_new($args)
{
    if( !xarVarFetch('what', 'str', $what, 'albums', XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('create', 'str', $submit, null, XARVAR_NOT_REQUIRED) ){ return false; }

    $itemtype = 0;
    if( $what == 'albums' ){ $itemtype = ALBUM_ITEMTYPE; }
    elseif( $what == 'files' ){ $itemtype = FILE_ITEMTYPE; }
    if( !Security::check(SECURITY_ADMIN, 'gallery', $itemtype) ){ return false; }

    $data = array();

    switch( $what )
    {
        case 'albums':
            $itemtype = 1;
            /*
                Update the gallery if form is submitted
            */
            if( !empty($submit) )
            {
                /*
                    Grab all gallery data
                */
                if( !xarVarFetch('name', 'pre:lower:ftoken:passthru:str:1:', $params['name']) ){ return false; }
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


                /*
                    First make sure the name if unique
                */
                $params['name'] = xarModAPIFunc('gallery', 'user', 'make_unique_album_name',
                    array(
                        'name' => $params['name']
                    )
                );

                /*
                    Create the gallery
                */
                $params['status'] = 'SUBMITTED';
                $album_id = xarModAPIFunc( 'gallery', 'admin', 'create_album', $params );
                if( $album_id > 0 )
                {
                    $settings_params['album_id'] = $album_id;
                    $settings_params['sort_order'] = "$sort_order|$sort_type";
                    $result = xarModAPIFunc( 'gallery', 'admin', 'update_album_settings', $settings_params );

                    xarModCallHooks('item', 'create', $album_id,
                        array(
                            'module'   => 'gallery',
                            'itemtype' => $itemtype,
                            'itemid' => $album_id
                        )
                    );
                }

                xarResponseRedirect(xarModURL('gallery', 'admin', 'view'));
                return true;
            }

            /*
                Set gallery defaults for creation
            */
            $data['album'] = array(
                'name' => '',
                'display_name' => '',
                'description' => '',
                'settings' => array(
                    'show_date' => '',
                    'items_per_page' => '10',
                    'cols_per_page' => '2',
                    'file_width' => '200px',
                    'file_quality' => '90',
                    'sort_order' => '',
                    'sort_type' => '',
                    'watermark_id' => ''
                )
            );
            if( !empty($album['settings']['show_date']) )
            { $album['settings']['show_date'] = 'checked="checked"'; }
            else { $album['settings']['show_date'] = ''; }

            $data['watermarks'] = xarModAPIFunc('gallery', 'user', 'get_watermarks');
            $data['sort_orders'] =& xarModAPIFunc('gallery', 'user', 'get_sort_orders');
            $data['sort_types'] = array('ASC' => 'Ascending', 'DESC' => 'Descending');

            break;

        case 'files':
            $itemtype = 2;
            if( !xarVarFetch('album_id', 'int', $album_id, null, XARVAR_NOT_REQUIRED) ){ return false; }

            if( !empty($submit) )
            {
                if( !xarVarFetch('summary', 'str', $summary, '', XARVAR_NOT_REQUIRED) ){ return false; }
                if( !xarVarFetch('links', 'array', $links) ){ return false; }

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

                if( !empty($file_id) )
                {
                    /*
                         update the DB entries and linkage
                    */
                    xarModAPIFunc('gallery', 'admin', 'update_file',
                        array(
                            'file_id' => $file_id,
                            'name' => $file_info['name'],
                            'file' => $file_info['file'],
                            'file_type' => $file_info['type'],
                            'file_size' => $file_info['size']
                        )
                    );

                    xarModAPIFunc('gallery', 'admin', 'link_files',
                        array(
                            'file_ids' => array($file_id),
                            'album_ids' => array_keys($links)
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

                xarResponseRedirect(
                    xarModURL('gallery', 'admin', 'view',
                        array(
                            'what' => $what,
                            'album_id' => $album_id
                        )
                    )
                );
                return false;
            }

            /*
                Set file defaults for creation
            */
            $data['file'] = array(
                'name' => '',
                'summary' => '',
                'file' => ''
            );

            $data['album_id'] = $album_id;
            $data['albums'] = xarModAPIFunc('gallery', 'user', 'get_albums');

            break;

        case 'watermarks':

            $itemtype = 3;

            $default = array(
                'type'      => 'image'
            );

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

            if( !xarVarFetch('reload', 'str', $action, 'create', XARVAR_NOT_REQUIRED) ){ return false; }
            if( !xarVarFetch('name', 'str', $name, '', XARVAR_NOT_REQUIRED) ){ return false; }
            if( !xarVarFetch('main', 'array', $main, $default, XARVAR_NOT_REQUIRED) ){ return false; }
            if( !xarVarFetch('thumbnail', 'array', $thumbnail, $default, XARVAR_NOT_REQUIRED) ){ return false; }
            /*
                If the type of the watermark changes make sure that it the watermark is init with the
                proper variables.
            */
            if( $main['type'] == 'image' and count($main) != count($image) )
                $main = $image;
            else if( $main['type'] == 'text' and count($main) != count($text) )
                $main = $text;
            if( $thumbnail['type'] == 'image' and count($thumbnail) != count($image) )
                $thumbnail = $image;
            else if( $thumbnail['type'] == 'text' and count($thumbnail) != count($text) )
                $thumbnail = $text;

            if( !empty($submit) )
            {
                /*
                    action must be equal to create to do anthing as we also reload this page to change
                    water mark types
                */
                if( $action == 'create' )
                {
                    $all_watermarks =@ unserialize(xarModGetVar('gallery', 'watermarks'));

                    if( !is_array($all_watermarks) )
                    {
                        $all_watermarks = array();
                    }
                    $new_watermark = array(
                        'name' => $name,
                        'main' => $main,
                        'thumbnail' => $thumbnail
                    );

                    $all_watermarks[] = $new_watermark;

                    xarModSetVar('gallery', 'watermarks', serialize($all_watermarks));

                    xarResponseRedirect(xarModURL('gallery', 'admin', 'view', array('what' => 'watermarks')));
                    return false;
                }
            }

            $data['types'] = array(
                'image' => 'Image',
                'text'  => 'Text'
            );

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
    $data['submit_text'] = 'Create';

    $hooks = xarModCallHooks('item', 'new', '',
        array(
            'module'   => 'gallery',
            'itemtype' => $itemtype
        ),
        'gallery'
    );
    if( empty($hooks) ) {
        $data['hooks'] = array();
    } else {
        $data['hooks'] = $hooks;
    }

    return xarTplModule('gallery', 'admin', 'new', $data, $what);
}
?>
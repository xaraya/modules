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

function gallery_user_view($args)
{
    ini_set('memory_limit', '64M');
    xarVarFetch('album_id', 'int', $album_id, null, XARVAR_NOT_REQUIRED);
    xarVarFetch('startnum', 'int', $startnum, null, XARVAR_NOT_REQUIRED);
    xarVarFetch('numitems', 'int', $numitems, null, XARVAR_NOT_REQUIRED);

    xarVarSetCached('modules.security', 'itemtype', ALBUM_ITEMTYPE);
    xarVarSetCached('modules.security', 'itemid', $album_id);

    if( !Security::check(SECURITY_READ, 'gallery', ALBUM_ITEMTYPE, $album_id) ){ return false; }

    /*
        First get gallery info. Default settings are in the gallery which will be used.
    */
    xarModAPILoad('security');
    $album = xarModAPIFunc('gallery', 'user', 'get_album',
        array(
            'album_id' => $album_id,
            'level'    => SECURITY_READ
        )
    );

    // Everything is album based. So if we dont have one lets get default settings
    if( empty($album) )
    {
        $album['settings'] = xarModAPIFunc('gallery', 'user', 'get_default_album_settings');
    }

    /*
        Set Meta Data for this gallery
    */
    if( xarModIsAvailable('metadata') && !empty($album['description']) )
    {
        $keywords = xarModAPIFunc('metadata', 'user', 'generatekeywords', array('content' => $album['description']));
        xarVarSetCached('Blocks.metadata', 'keywords', $keywords);
        xarVarSetCached('Blocks.metadata', 'description', $album['description']);
    }

    /*
        Set defaults
    */
    if( is_null($numitems) )
    {
        if( !empty($album['settings']['items_per_page']) )
        {
            $numitems = $album['settings']['items_per_page'];
        }
        else
        {
            $numitems = xarModGetVar('gallery', 'cols_per_page');
        }
    }

    /*
        Build the sort options
    */
    $sort = "{$album['settings']['sort_order']} {$album['settings']['sort_type']}";
    $watermark = xarModAPIFunc('gallery', 'user', 'get_watermark',
        array('watermark_id' => $album['settings']['watermark_id'])
    );
    /*
        Get and Count files
    */
    $files = xarModAPIFunc('gallery', 'user', 'get_files',
        array(
            'album_id' => $album_id,
            'startnum' => $startnum,
            'numitems' => $numitems,
            'states'   => array('APPROVED'),
            'sort'     => isset($sort) ? $sort : null
        )
    );

    $count = xarModAPIFunc('gallery', 'user', 'count_files',
        array(
            'album_id' => $album_id,
            'states'   => array('APPROVED')
        )
    );

    /*
        Process files. Generate display links. Set Today's file if needed.
    */
    $now = date('Ymd');
    $today = '';
    $today_found = false;
    foreach( $files as $key => $file )
    {
        $files[$key]['link'] = xarModURL('gallery', 'user', 'display',
            array(
                'album_id' => $album_id,
                'file_id'  => $file['file_id']
            )
        );

        if( $now == date('Ymd', $file['created']) && !$today_found && $album['settings']['show_date'] )
        {
            $today = $files[$key];
            unset($files[$key]);
        }
    }

    $data = array();

    /*
        Generate Pager
    */
    $url_template = xarModURL('gallery', 'user', 'view',
        array(
            'album_id' => $album_id,
            'startnum' => '%%'
        )
    );
    $data['pager'] = xarTplGetPager($startnum, $count, $url_template, $numitems);

    /*
        Call Display hooks
    */
    $hooks = xarModCallHooks('item', 'display', $album_id,
        array(
            'module'   => 'gallery',
            'itemtype' => ALBUM_ITEMTYPE,
            'itemid' => $album_id,
            'returnurl' => xarModURL('gallery', 'user', 'view',
                array(
                    'album_id' => $album_id
                )
            )
        ),
        'gallery'
    );
    if( empty($hooks) ) {
        $data['hooks'] = array();
    } else {
        $data['hooks'] = $hooks;
    }

    // Set Page title
    if( !empty($album['display_name']) )
    {
        xarTplSetPageTitle($album['display_name']);
    }

    /*
        Setup rest of template variables.
    */
    if( !empty($watermark) )
    {
        $data['main_watermark'] = $watermark['main'];
        $data['watermark'] = $watermark['thumbnail'];
    }

    $data['album'] = $album;
    $data['files_path'] = xarModGetVar('gallery', 'file_path');
    $data['today']  = $today;
    $data['files'] = $files;
    $data['num_cols'] = xarModGetVar('gallery', 'cols_per_page');

    //if( xarModGetVar('gallery', 'enable_lightbox') == true ){
        $template = 'lightbox';
    /*} else {
        $template = null;
    }*/

    return xarTplModule('gallery', 'user', 'view', $data, $template);
}
?>
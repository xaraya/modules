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
	Init. block
*/
function gallery_albumsblock_init()
{
    return array(
        'numitems' => 5,
        'albums' => array(),
        'limit' => 10,
        'order' => 'name'
    );
}

/**
	Get information on block
*/
function gallery_galleriesblock_info()
{
    // Values
    return array(
        'text_type' => 'Albums',
        'module' => 'gallery',
        'text_type_long' => 'Albums Block',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true
    );
}

function gallery_albumsblock_display($blockinfo)
{
    // Get variables from content block
    if( !is_array($blockinfo['content']) )
    {
        $vars = @unserialize($blockinfo['content']);
    }
    else
    {
        $vars = $blockinfo['content'];
    }

    extract($vars);

    // Process Block Arguments
    $args = array();
    $args['numitems'] = $vars['numitems'];

    if( !in_array(-1, $vars['albums']) )
        $args['album_ids'] = $vars['albums'];

    $args['sort'] = $vars['order']; // . ' DESC';
    $args['states'] = array('APPROVED');

    // Get Galleries
    $data['albums'] = xarModAPIFunc('gallery', 'user', 'get_albums', $args);
    foreach( $data['albums'] as $album_id => $album )
    {
        $data['albums'][$album_id]['link'] = xarModURL('gallery', 'user', 'view',
            array(
                'album_id' => $album_id
            )
        );
    }

    $data['file_path'] = xarModGetVar('gallery', 'file_path');

    // Return data, not rendered content.
    $blockinfo['content'] = $data;
    if( !empty($blockinfo['content']) )
    {
        return $blockinfo;
    }
}

/**
	Modify blocks built-in params
*/
function gallery_albumsblock_modify($blockinfo)
{
    // Get current content
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    if( !isset($vars['numitems']) )
        $vars['numitems'] = 5;

    if( !isset($vars['limit']) )
        $vars['limit'] = 10;

    if( !isset($vars['order']) )
        $vars['order'] = 'name';

    // Get the strips
    $vars['all_albums'] = xarModAPIFunc('gallery', 'user', 'get_albums',
        array(
            'numitems' => $vars['limit']
        )
    );
    $vars['all_albums'][-1] = array('album_id' => -1, 'name' => "All");
    ksort($vars['all_albums']);

    $vars['orders'] = array(
        'name'   => 'Name',
        'album_id'  => 'ID',
        'random'    => 'Random'
    );

    $vars['blockid'] = $blockinfo['bid'];

    // Return output
    return $vars;
}

/**
	Update blocks built-in params
*/
function gallery_albumsblock_update($blockinfo)
{
    $vars = array();
    if( !xarVarFetch('numitems', 'int', $vars['numitems'], 1, XARVAR_NOT_REQUIRED) ){ return; }
    if( !xarVarFetch('albums', 'array', $vars['albums'], array(), XARVAR_NOT_REQUIRED) ){ return; }
    if( !xarVarFetch('limit',  'int:1', $vars['limit'], 10, XARVAR_NOT_REQUIRED) ){ return; }
    if( !xarVarFetch('order',  'str',   $vars['order'], array(), XARVAR_NOT_REQUIRED) ){ return; }

    $blockinfo['content'] = $vars;

    return $blockinfo;
}
?>
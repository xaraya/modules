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
	Modify blocks built-in params
*/

function gallery_photosblock_modify($blockinfo)
{
    // Get current content
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    if( !isset($vars['limit']) )
        $vars['limit'] = 10;

    if( !isset($vars['order']) )
        $vars['order'] = 'date';

    // Get the strips
    $vars['all_galleries'] = xarModAPIFunc('gallery', 'user', 'get_galleries');

    if( !isset($vars['limit']) )
        $vars['limit'] = 10;

    $vars['all_files'] = xarModAPIFunc('gallery', 'user', 'get_files',
        array(
            'numitems' => $vars['limit'],
            'states'   => array('APPROVED')
        )
    );

    $vars['orders'] = array(
        'created'   => 'Date',
        'file_id'  => 'ID',
        'random'    => 'Random'
    );

    $vars['blockid'] = $blockinfo['bid'];

    // Return output
    return $vars;
}

/**
	Update blocks built-in params
*/
function gallery_photosblock_update($blockinfo)
{
    $vars = array();
    if( !xarVarFetch('numitems', 'int', $vars['numitems'], 1, XARVAR_NOT_REQUIRED) ){ return; }
    if( !xarVarFetch('galleries', 'array', $vars['galleries'], array(), XARVAR_NOT_REQUIRED) ){ return; }
    if( !xarVarFetch('limit',  'int:1', $vars['limit'], 10, XARVAR_NOT_REQUIRED) ){ return; }
    if( !xarVarFetch('files', 'array', $vars['files'], array(), XARVAR_NOT_REQUIRED) ){ return; }
    if( !xarVarFetch('order',  'str',   $vars['order'], array(), XARVAR_NOT_REQUIRED) ){ return; }

    $blockinfo['content'] = $vars;

    return $blockinfo;
}

/**
	Init. block
*/
function gallery_photosblock_init()
{
    return array(
        'numitems' => 5,
        'galleries' => array(),
        'limit' => 10,
        'files' => array(),
        'order' => array()
    );
}

/**
	Get information on block
*/
function gallery_photosblock_info()
{
    // Values
    return array(
        'text_type' => 'Photos',
        'module' => 'gallery',
        'text_type_long' => 'Photos Block',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => false
    );
}

/**
	Display block
*/
function gallery_photosblock_display($blockinfo)
{
    // Security Check
    if(!xarSecurityCheck('ViewBaseBlocks',0,'Block',"All:$blockinfo[title]:All")) return;

    // Get variables from content block
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    if( !empty($vars) ){
        extract($vars);
    }

    $data = array();

    // Process Block Arguments
    $args = array();
    $args['numitems'] = $vars['numitems'];
    if( !in_array(-1, $vars['galleries']) )
        $args['gallery_ids'] = $vars['galleries'];
    if( !in_array(-1, $vars['files']) )
        $args['file_ids'] = $vars['files'];
    $args['sort'] = $vars['order'] . ' DESC';

    // Get Files
    $args['states'] = array('APPROVED');

    $data['files'] = xarModAPIFunc('gallery', 'user', 'get_files', $args);
    if( count($data['files']) > 0 )
    {
        foreach( $data['files'] as $photo_id => $photo )
        {
            $data['files'][$photo_id]['link'] = xarModURL('gallery', 'user', 'display',
                array(
                    'gallery_id' => $photo['gallery_id'],
                    'file_id' => $photo['file_id']
                )
            );
        }
    }
    $data['file_path'] = xarModGetVar('gallery', 'file_path');

    // Return data, not rendered content.
    $blockinfo['content'] = $data;
    if (!empty($blockinfo['content'])) {
        return $blockinfo;
    }

    return;
}
?>
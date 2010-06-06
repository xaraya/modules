<?php
/**
 * Path Block
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Path Module
 * @link http://www.xaraya.com/index.php/release/eid/1150
 * @author potion <ryan@webcommunicate.net>
 */

/**
 * initialise block
 * @return array
 */
function path_firstblock_init()
{
    return array(
        'numitems' => 5
    );
}

/**
 * get information on block
 * @return array
 */
function path_firstblock_info()
{
    // Values
    return array('text_type' => 'First',
        'module' => 'path',
        'text_type_long' => 'Show first path items (alphabetical)',
        'allow_multiple' => true,
        'form_path' => false,
        'form_refresh' => false,
        'show_preview' => true);
}

/**
 * display block
 * @return array blockinfo
 */
function path_firstblock_display($blockinfo)
{
// TODO: Security check
//    if (!xarSecurityCheck('ReadExampleBlock', 1, 'Block', $blockinfo['title'])) {return;}

    // Get variables from path block.
    // Path is a serialized array for legacy support, but will be
    // an array (not serialized) once all blocks have been converted.
    if (!is_array($blockinfo['path'])) {
        $data = @unserialize($blockinfo['path']);
    } else {
        $data = $blockinfo['path'];
    }

    // Defaults
    if (empty($data['numitems'])) {
        $data['numitems'] = 5;
    }
    $data['blockid'] = $blockinfo['bid'];

    // we'll retrieve the items directly in the template here

    // Just return the template data.
    $blockinfo['path'] = $data;

    return $blockinfo;
}

?>

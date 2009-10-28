<?php
/**
 * Dynamic Data Example Block
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author mikespub <mikespub@xaraya.com>
 */

/**
 * initialise block
 * @return array
 */
function dyn_example_firstblock_init()
{
    return array(
        'numitems' => 5
    );
}

/**
 * get information on block
 * @return array
 */
function dyn_example_firstblock_info()
{
    // Values
    return array('text_type' => 'First',
        'module' => 'dyn_example',
        'text_type_long' => 'Show first dyn_example items (alphabetical)',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true);
}

/**
 * display block
 * @return array blockinfo
 */
function dyn_example_firstblock_display($blockinfo)
{
// TODO: Security check
//    if (!xarSecurityCheck('ReadExampleBlock', 1, 'Block', $blockinfo['title'])) {return;}

    // Get variables from content block.
    // Content is a serialized array for legacy support, but will be
    // an array (not serialized) once all blocks have been converted.
    if (!is_array($blockinfo['content'])) {
        $data = @unserialize($blockinfo['content']);
    } else {
        $data = $blockinfo['content'];
    }

    // Defaults
    if (empty($data['numitems'])) {
        $data['numitems'] = 5;
    }
    $data['blockid'] = $blockinfo['bid'];

    // we'll retrieve the items directly in the template here

    // Just return the template data.
    $blockinfo['content'] = $data;

    return $blockinfo;
}

?>

<?php
/**
 * shop Block
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage shop Module
 * @link http://www.xaraya.com/index.php/release/eid/1031
 * @author potion <ryan@webcommunicate.net>
 */

/**
 * initialise block
 * @return array
 */
function shop_firstblock_init()
{
    return array(
        'numitems' => 5
    );
}

/**
 * get information on block
 * @return array
 */
function shop_firstblock_info()
{
    // Values
    return array('text_type' => 'First',
        'module' => 'shop',
        'text_type_long' => 'Show first shop items (alphabetical)',
        'allow_multiple' => true,
        'form_shop' => false,
        'form_refresh' => false,
        'show_preview' => true);
}

/**
 * display block
 * @return array blockinfo
 */
function shop_firstblock_display($blockinfo)
{
// TODO: Security check
//    if (!xarSecurityCheck('ReadExampleBlock', 1, 'Block', $blockinfo['title'])) {return;}

    // Get variables from shop block.
    // shop is a serialized array for legacy support, but will be
    // an array (not serialized) once all blocks have been converted.
    if (!is_array($blockinfo['shop'])) {
        $data = @unserialize($blockinfo['shop']);
    } else {
        $data = $blockinfo['shop'];
    }

    // Defaults
    if (empty($data['numitems'])) {
        $data['numitems'] = 5;
    }
    $data['blockid'] = $blockinfo['bid'];

    // we'll retrieve the items directly in the template here

    // Just return the template data.
    $blockinfo['shop'] = $data;

    return $blockinfo;
}

?>

<?php
/**
 * shop Block modification
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
 * modify block settings
 * This function is called via the blocks module. It shows a form in the block instance screen
 * @return array
 */
function shop_firstblock_modify($blockinfo)
{
    // Get current shop
    if (!is_array($blockinfo['shop'])) {
        $vars = unserialize($blockinfo['shop']);
    } else {
        $vars = $blockinfo['shop'];
    }

    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    }

    // Send shop to template
    return array('numitems' => $vars['numitems'], 'blockid' => $blockinfo['bid']);
}

/**
 * update block settings
 *
 * @param numitems The number of items to show
 * @return array $blockinfo with the information held by the block
 */
function shop_firstblock_update($blockinfo)
{
    // We get the numitems. It is placed into $vars['numitems']
    if (!xarVarFetch('numitems', 'int:0', $vars['numitems'], 5, XARVAR_DONT_SET)) {return;}

    $blockinfo['shop'] = $vars;

    return $blockinfo;
}

?>

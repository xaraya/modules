<?php
/**
 * Shopping Block
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage shopping
 * @author Shopping module development team 
 */

/**
 * modify block settings
 */
function shopping_shoppingcartblock_modify($blockinfo)
{ 
    // Get current content
    $vars = @unserialize($blockinfo['content']); 
    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    } 
    // Send content to template
    $output = xarTplBlock('shopping', 'modify-shoppingcart', array('numitems' => $vars['numitems'], 'blockid' => $blockinfo['bid'])); 
    // Return output
    return $output;
} 

/**
 * update block settings
 */
function shopping_shoppingcartblock_update($blockinfo)
{
    if (!xarVarFetch('numitems', 'isset', $vars['numitems'], NULL, XARVAR_DONT_SET)) return;

    $blockinfo['content'] = serialize($vars);

    return $blockinfo;
} 

?>
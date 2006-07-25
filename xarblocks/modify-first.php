<?php
/**
 * Dynamic Data Example Block modification
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/732.html
 * @author mikespub <mikespub@xaraya.com>
 */

/**
 * modify block settings
 */
function accessmethods_firstblock_modify($blockinfo)
{ 
    // Get current content
    if (!is_array($blockinfo['content'])) {
        $vars = unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    } 

    // Send content to template
    return array('numitems' => $vars['numitems'], 'blockid' => $blockinfo['bid']);
} 

/**
 * update block settings
 */
function accessmethods_firstblock_update($blockinfo)
{
    if (!xarVarFetch('numitems', 'int:0', $vars['numitems'], 5, XARVAR_DONT_SET)) {return;}

    $blockinfo['content'] = $vars;

    return $blockinfo;
} 

?>

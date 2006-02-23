<?php
/**
 * Release Block
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage release
 * @author Release module development team 
 */

/**
 * modify block settings
 * @return array
 */
function release_latestblock_modify($blockinfo)
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
 * @param int numitems
 * @return array
 */
function release_latestblock_update($blockinfo)
{
    if (!xarVarFetch('numitems', 'int:0', $vars['numitems'], 5, XARVAR_DONT_SET)) {return;}

    $blockinfo['content'] = $vars;

    return $blockinfo;
} 

?>
<?php
/**
 * Release Block
 * 
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
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
    if (!isset($vars['shownonfeeditems']) || empty($vars['shownonfeeditems'])) {
        $vars['shownonfeeditems'] = 0;
    } 

    // Send content to template
    return array('numitems' => $vars['numitems'],
                 'shownonfeeditems' => $vars['shownonfeeditems'],
                 'blockid' => $blockinfo['bid']);
} 

/**
 * update block settings
 * @param int numitems
 * @return array
 */
function release_latestblock_update($blockinfo)
{
    if (!xarVarFetch('numitems', 'int:0', $vars['numitems'], 5, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('shownonfeeditems', 'checkbox', $vars['shownonfeeditems'], false, XARVAR_DONT_SET)) {return;}
    $vars['shownonfeeditems'] = $vars['shownonfeeditems']?1:0;
    $blockinfo['content'] = $vars;

    return $blockinfo;
} 

?>
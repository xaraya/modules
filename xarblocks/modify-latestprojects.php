<?php
/**
 * Release Block - Latest Project Extensions
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
function release_latestprojectsblock_modify($blockinfo)
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
    if (!isset($vars['showonlists']) || empty($vars['showonlists'])) {
        $vars['showonlists'] = 0;
    } 

    // Send content to template
    return array('numitems' => $vars['numitems'],
                 'showonlists' => $vars['showonlists'],
                 'blockid' => $blockinfo['bid']);
} 

/**
 * update block settings
 * @param int numitems
 * @return array
 */
function release_latestprojectsblock_update($blockinfo)
{
    if (!xarVarFetch('numitems', 'int:0', $vars['numitems'], 5, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('showonlists', 'checkbox', $vars['showonlists'], false, XARVAR_DONT_SET)) {return;}
    $vars['showonlists'] = $vars['showonlists']?1:0;
    $blockinfo['content'] = $vars;

    return $blockinfo;
} 

?>
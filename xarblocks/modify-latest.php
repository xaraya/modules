<?php
/**
 * Latest Block
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsgroups
 * @author mikespub
 */

/**
 * modify block settings
 */
function newsgroups_latestblock_modify($blockinfo)
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
    if (empty($vars['group'])) {
        $vars['group'] = '';
    }

    $items = xarModAPIFunc('newsgroups','user','getgroups');

    // Send content to template
    return array(
        'items'    => $items,
        'group'    => $vars['group'],
        'numitems' => $vars['numitems'],
        'blockid'  => $blockinfo['bid']
    );
} 

/**
 * update block settings
 */
function newsgroups_latestblock_update($blockinfo)
{
    $vars = array();
    if (!xarVarFetch('group', 'str', $vars['group'], '', XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('numitems', 'int', $vars['numitems'], 5, XARVAR_DONT_SET)) {return;}
    $blockinfo['content'] = $vars;
    return $blockinfo;
} 

?>

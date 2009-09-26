<?php

/**
 * File: $Id$
 *
 * Displays a crumb-trail block
 * Shows the visitor's current position in the page hierarchy
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2004 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage Xarpages Module
 * @author Jason Judge
*/

/**
 * Modify Function to the Blocks Admin
 * @param $blockinfo array (serialized or unserialized)
 */

function xarpages_crumbblock_modify($blockinfo)
{
    // Get current content
    if (!is_array($blockinfo['content'])) {
        $vars = unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // Defaults
    if (!isset($vars['include_root'])) {$vars['include_root'] = false;}
    if (!isset($vars['root_pids'])) {$vars['root_pids'] = array();}

    // Get a list of all pages for the drop-downs.
    // Get the tree of all pages, without the DD for speed.
    $vars['all_pages'] = xarMod::apiFunc(
        'xarpages', 'user', 'getpagestree',
        array('dd_flag' => false, 'key' => 'pid')
    );

    // Implode the names for each page into a path for display.
    // TODO: move this into getpagestree
    foreach ($vars['all_pages']['pages'] as $key => $page) {
        $vars['all_pages']['pages'][$key]['slash_separated'] =  '/' . implode('/', $page['namepath']);
    }

    // Get the descriptions together for the current root pids.
    // TODO: we could prune the 'add root page' list so it only includes
    // the pages which are not yet under one of the selected root pages.
    // That would just be an extra little usability touch.
    $vars['root_pids'] = array_flip($vars['root_pids']);
    foreach($vars['root_pids'] as $key => $value) {
        if (isset($vars['all_pages']['pages'][$key])) {
            $vars['root_pids'][$key] = $vars['all_pages']['pages'][$key]['slash_separated'];
        } else {
            $vars['root_pids'][$key] = xarML('Unknown');
        }
    }

    $vars['bid'] = $blockinfo['bid'];

    return $vars;
}

/**
 * Updates the Block config from the Blocks Admin
 * @param $blockinfo array containing title,content
 */
function xarpages_crumbblock_update($blockinfo)
{
    // Ensure content is an array.
    // TODO: remove this once all blocks can accept content arrays.
    if (!is_array($blockinfo['content'])) {
        $blockinfo['content'] = unserialize($blockinfo['content']);
    }

    // Reference to content array.
    $vars =& $blockinfo['content'];

    if (xarVarFetch('include_root', 'bool', $include_root, false, XARVAR_NOT_REQUIRED)) {
        $vars['include_root'] = $include_root;
    }

    // The root pages define sections of the page landscape that this block applies to.
    if (!isset($vars['root_pids'])) {
        $vars['root_pids'] = array();
    }
    if (xarVarFetch('new_root_pid', 'int:0', $new_root_pid, 0, XARVAR_NOT_REQUIRED) && !empty($new_root_pid)) {
        $vars['root_pids'][] = $new_root_pid;
    }
    if (xarVarFetch('remove_root_pid', 'list:int:1', $remove_root_pid, array(), XARVAR_NOT_REQUIRED) && !empty($remove_root_pid)) {
        // Easier to check with the keys and values flipped.
        $vars['root_pids'] = array_flip($vars['root_pids']);
        foreach($remove_root_pid as $remove) {
            if (isset($vars['root_pids'][$remove])) {
                unset($vars['root_pids'][$remove]);
            }
        }
        // Flip keys and values back.
        $vars['root_pids'] = array_flip($vars['root_pids']);
        // Reorder the keys.
        $vars['root_pids'] = array_values($vars['root_pids']);
    }

    return $blockinfo;
}

?>
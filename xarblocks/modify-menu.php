<?php

/**
 * File: $Id$
 *
 * Displays a menu block
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

function xarpages_menublock_modify($blockinfo)
{
    // Get current content
    if (!is_array($blockinfo['content'])) {
        $vars = unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // Defaults
    if (!isset($vars['multi_homed'])) {$vars['multi_homed'] = true;}
    if (!isset($vars['current_source'])) {$vars['current_source'] = 'AUTO';}
    if (!isset($vars['default_pid'])) {$vars['default_pid'] = 'AUTO';}
    if (!isset($vars['max_level'])) {$vars['max_level'] = 0;}
    if (!isset($vars['start_level'])) {$vars['start_level'] = 0;}
    if (!isset($vars['root_pids'])) {$vars['root_pids'] = array();}
    if (!isset($vars['prune_pids'])) {$vars['prune_pids'] = array();}

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

    $vars['prune_pids'] = array_flip($vars['prune_pids']);
    foreach($vars['prune_pids'] as $key => $value) {
        if (isset($vars['all_pages']['pages'][$key])) {
            $vars['prune_pids'][$key] = $vars['all_pages']['pages'][$key]['slash_separated'];
        } else {
            $vars['prune_pids'][$key] = xarML('Unknown');
        }
    }

    $vars['bid'] = $blockinfo['bid'];

    return $vars;
}

/**
 * Updates the Block config from the Blocks Admin
 * @param $blockinfo array containing title,content
 */
function xarpages_menublock_update($blockinfo)
{
    // Ensure content is an array.
    // TODO: remove this once all blocks can accept content arrays.
    if (!is_array($blockinfo['content'])) {
        $blockinfo['content'] = unserialize($blockinfo['content']);
    }

    // Reference to content array.
    $vars =& $blockinfo['content'];

    if (xarVarFetch('multi_homed', 'checkbox', $multi_homed, 1, XARVAR_NOT_REQUIRED)) {
        $vars['multi_homed'] = $multi_homed;
    }

    // AUTO: the block picks up the page from cache Blocks.xarpages/current_pid.
    // DEFAULT: the block always uses the default page.
    // AUTODEFAULT: same as AUTO, but use the default page rather than NULL if outside and root page
    if (xarVarFetch('current_source', 'pre:upper:passthru:enum:AUTO:DEFAULT:AUTODEFAULT', $current_source, 'AUTO', XARVAR_NOT_REQUIRED)) {
        $vars['current_source'] = $current_source;
    }

    // The default page if none found by any other method.
    if (xarVarFetch('default_pid', 'int:0', $default_pid, 0, XARVAR_NOT_REQUIRED)) {
        $vars['default_pid'] = $default_pid;
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

    // The pruning pages define sections of the page landscape that this block applies to.
    if (!isset($vars['prune_pids'])) {
        $vars['prune_pids'] = array();
    }
    if (xarVarFetch('new_prune_pid', 'int:0', $new_prune_pid, 0, XARVAR_NOT_REQUIRED) && !empty($new_prune_pid)) {
        $vars['prune_pids'][] = $new_prune_pid;
    }
    if (xarVarFetch('remove_prune_pid', 'list:int:1', $remove_prune_pid, array(), XARVAR_NOT_REQUIRED) && !empty($remove_prune_pid)) {
        // Easier to check with the keys and values flipped.
        $vars['prune_pids'] = array_flip($vars['prune_pids']);
        foreach($remove_prune_pid as $remove) {
            if (isset($vars['prune_pids'][$remove])) {
                unset($vars['prune_pids'][$remove]);
            }
        }
        // Flip keys and values back.
        $vars['prune_pids'] = array_flip($vars['prune_pids']);
        // Reorder the keys.
        $vars['prune_pids'] = array_values($vars['prune_pids']);
    }

    // The maximum number of levels that are displayed.
    // This value does not affect the tree data, but is passed to the menu rendering
    // templates to make its own decision on how to truncate the menu.
    if (xarVarFetch('max_level', 'int:0:999', $max_lavel, 0, XARVAR_NOT_REQUIRED)) {
        $vars['max_level'] = $max_lavel;
    }

    // The start level.
    // Hide the menu if the current page is below this level.
    if (xarVarFetch('start_level', 'int:0:999', $start_lavel, 0, XARVAR_NOT_REQUIRED)) {
        $vars['start_level'] = $start_lavel;
    }

    return $blockinfo;
}

?>
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

    // Get a list of all pages for the drop-downs.
    // Get the tree of all pages, without the DD for speed.
    $vars['all_pages'] = xarModAPIfunc('xarpages', 'user', 'getpagestree', array('dd_flag' => false));

    // Implode the names for each page into a path for display.
    foreach ($vars['all_pages']['pages'] as $key => $page) {
        $vars['all_pages']['pages'][$key]['slash_separated'] =  '/' . implode('/', $page['namepath']);
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

    if (xarVarFetch('multi_homed', 'bool', $multi_homed, true, XARVAR_NOT_REQUIRED)) {
        $vars['multi_homed'] = $multi_homed;
    }

    // AUTO: the block picks up the page from cache Blocks.xarpages/current_pid.
    // DEFAULT: the block always uses the default page.
    if (xarVarFetch('current_source', 'pre:upper:passthru:enum:AUTO:DEFAULT', $current_source, 'AUTO', XARVAR_NOT_REQUIRED)) {
        $vars['current_source'] = $current_source;
    }

    // The default page if none found by any other method.
    if (xarVarFetch('default_pid', 'int:0', $default_pid, 0, XARVAR_NOT_REQUIRED)) {
        $vars['default_pid'] = $default_pid;
    }

    return $blockinfo;
}

?>
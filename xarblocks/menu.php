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
 * init func
 */

function xarpages_menublock_init()
{
    return array(
        'multi_homed' => true,
        'current_source' => 'AUTO', // Other values: 'DEFAULT'
        'default_pid' => 0 // 0 == 'None'
    );
}

/**
 * Block info array
 */

function xarpages_menublock_info()
{
    return array(
        'text_type' => 'Content',
        'text_type_long' => 'Xarpages Menu Block',
        'module' => 'xarpages',
        'func_update' => 'xarpages_menublock_update',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true,
        'notes' => 'no notes'
    );
}

/**
 * Display func.
 * @param $blockinfo array
 * @returns $blockinfo array
 * @todo Option to display the menu even when not on a relevant page
 */

function xarpages_menublock_display($blockinfo)
{
    // Security Check
    // TODO: remove this check once it goes into the blocks centrally.
    //if (!xarSecurityCheck('ViewBlocks', 0, 'Block', 'xarpages:menu:' . $blockinfo['name'])) {return;}

    // TODO:
    // We want a few facilities:
    // 1. Set a root higher than the real tree root. Pages will only
    //    be displayed once that root is reached. Effectively set one
    //    or more trees, at any depth, that this menu will cover.
    // 2. Set a 'max depth' value, so only a preset max number of levels
    //    are rendered in a tree.
    // [1 and 2 are a kind of "view window" for levels]
    // 3. Set behaviour when no current page in the xarpages module is
    //    displayed, e.g. hide menu, show default tree or page etc.

    // Get variables from content block.
    if (!is_array($blockinfo['content'])) {
        $blockinfo['content'] = unserialize($blockinfo['content']);
    }

    // Pointer to simplify referencing.
    $vars =& $blockinfo['content'];

    // To start with, we need to know the current page.
    // It could be set (fixed) for the block, passed in
    // via the page cache, or simply not present.
    $pid = 0;
    if (empty($vars['current_source']) || $vars['current_source'] == 'AUTO') {
        // Automatic: that means look at the page cache.
        if (xarVarIsCached('Blocks.xarpages', 'current_pid')) {
            $cached_pid = xarVarGetCached('Blocks.xarpages', 'current_pid');
            // Make sure it is numeric.
            if (isset($cached_pid) && is_numeric($cached_pid)) {
                $pid = $cached_pid;
            }
        }
    }

    // Now we may or may not have a page ID.
    // If the page is not set, then check for a default.
    if (empty($pid) && !empty($vars['default_pid'])) {
        // Set the current page to be the default.
        $pid = $vars['default_pid'];

        // Note: depending on the value of 'default_type', this pid
        // may be treated as a current page, or the root of a default
        // tree.
        $default_flag = (isset($vars['default_type']) ? $vars['default_type'] : 'PAGE');
    } else {
        // TODO: not sure, but could a module want to call up a tree without
        // setting a current page?
        $default_flag = 'PAGE';
    }

    // The page details *may* have been cached, if
    // we are in the xarpages module.
    if (xarVarIsCached('Blocks.xarpages', 'pagedata')) {
        // Pages are cached?
        $pagedata = xarVarGetCached('Blocks.xarpages', 'pagedata');

        // If the cached tree does not contain the current page,
        // then we cannot use it.
        if (!isset($pagedata['pages'][$pid])) {
            $pagedata = array();
        }
    }

    // If there is no pid, then we have no page or tree to display.
    if (empty($pid)) {return;}
    
    // If we don't have any page data, then fetch it now.
    if (empty($pagedata)) {
        // Get the page data here now.
        $pagedata = xarModAPIfunc(
            'xarpages', 'user', 'getpagestree',
            array(
                'tree_contains_pid' => $pid,
                'dd_flag' => true,
                'key' => 'pid',
                'status' => 'ACTIVE,EMPTY'
            )
        );

        // If $pagedata is empty, then we have an invalid ID or
        // no permissions. Return NULL if so, suppressing the block.
        if (empty($pagedata)) {return;}

        // Cache the data now we have gone to the trouble of fetching the tree.
        // Only cache it if the cache is empty to start with.
        if (!xarVarIsCached('Blocks.xarpages', 'pagedata')) {
            xarVarSetCached('Blocks.xarpages', 'pagedata', $pagedata);
        }
    }

    // TODO: handle privileges for pages somewhere. The user/display
    // function handles it for the current page, but there is not
    // point the block providing links to pages that cannot be
    // accessed.
    
    // Here we add the various flags to the pagedata,
    // assuming we have a current page.
    if ($default_flag == 'PAGE') {
        $pagedata = xarModAPIfunc(
            'xarpages', 'user', 'addcurrentpageflags',
            array('pagedata' => $pagedata, 'pid' => $pid)
           
         );
    }

    // Pass the page data into the block.
    // Merge it in with the existing block details.
    // TODO: It may be quicker to do it the other way around?
    $vars = array_merge($vars, $pagedata);

    // TODO: allow the root page to be set at a variety of points.
    // If, for example, the root page is set several levels up the tree,
    // then the menu will remain static and open at just one level, 
    // until a page within that sub-tree is selected.
    // Set the root page.
    if (!empty($vars['ancestors'])) {
        $vars['root_page'] =& reset($vars['ancestors']);
    } else {
        return;
    }

    return $blockinfo;
}

?>
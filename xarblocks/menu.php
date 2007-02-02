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
        'default_pid' => 0, // 0 == 'None'
        'root_pids' => array(),
        'prune_pids' => array(),
        'max_level' => 0,
        'start_level' => 0,
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
    //    or more trees, at any depth, that this menu will cover. [DONE]
    // 2. Set a 'max depth' value, so only a preset max number of levels
    //    are rendered in a tree. [DONE]
    // [1 and 2 are a kind of "view window" for levels]
    // 3. Set behaviour when no current page in the xarpages module is
    //    displayed, e.g. hide menu, show default tree or page etc. [DONE]
    // 4. Allow the page tree to be pruned at arbitrary specified
    //    pages. That would allow sections of the tree to be pruned
    //    from one menu and added to another (i.e. split menus).
    //    This will also move the current page, if it happens to be in the
    //    pruned section, down to the pruning page. [done]

    // Get variables from content block.
    if (!is_array($blockinfo['content'])) {
        $blockinfo['content'] = unserialize($blockinfo['content']);
    }

    // Pointer to simplify referencing.
    $vars =& $blockinfo['content'];

    if (!empty($vars['root_pids']) && is_array($vars['root_pids'])) {
        $root_pids = $vars['root_pids'];
    } else {
        $root_pids = array();
    }

    if (!empty($vars['prune_pids']) && is_array($vars['prune_pids'])) {
        $prune_pids = $vars['prune_pids'];
    } else {
        $prune_pids = array();
    }

    // To start with, we need to know the current page.
    // It could be set (fixed) for the block, passed in
    // via the page cache, or simply not present.
    $pid = 0;
    if (empty($vars['current_source']) || $vars['current_source'] == 'AUTO' || $vars['current_source'] == 'AUTODEFAULT') {
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
    }

    // The page details *may* have been cached, if
    // we are in the xarpages module, or have several
    // blocks on the same page showing the same tree.
    if (xarVarIsCached('Blocks.xarpages', 'pagedata')) {
        // Pages are cached?
        // The 'serialize' hack ensures we have a proper copy of the
        // paga data, which is a self-referencing array. If we don't
        // do this, then any changes we make will affect the stored version.
        $pagedata = unserialize(serialize(xarVarGetCached('Blocks.xarpages', 'pagedata')));
        //$pagedata = unserialize(serialize($pagedata));
        // If the cached tree does not contain the current page,
        // then we cannot use it.
        if (!isset($pagedata['pages'][$pid])) {
            $pagedata = array();
        }
    }

    // If there is no pid, then we have no page or tree to display.
    if (empty($pid)) {return;}

    // If necessary, check whether the current page is under one of the
    // of the allowed root pids.
    if (!empty($root_pids)) {
        if (!xarModAPIfunc('xarpages', 'user', 'pageintrees', array('pid' => $pid, 'tree_roots' => $root_pids))) {
            // Not under a root.
            // If the mode is AUTO then leave the menu blank.
            if ($vars['current_source'] == 'AUTO' || $vars['current_source'] == 'DEFAULT' || empty($vars['default_pid'])) {
                return;
            } else {
                // Use the default page instead.
                $pid = $vars['default_pid'];
                $pagedata = array();
            }
        }
    }

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
        if (empty($pagedata['pages'])) {return;}

        // Cache the data now we have gone to the trouble of fetching the tree.
        // Only cache it if the cache is empty to start with. We only cache a complete
        // tree here, so if any other blocks need it, it contains all possible
        // pages we could need in that tree.
        if (!xarVarIsCached('Blocks.xarpages', 'pagedata')) {
            xarVarSetCached('Blocks.xarpages', 'pagedata', $pagedata);
        }
    }


    // If the user has set a 'start level' then make sure the page sits at that level or above.
    // TODO: take into account the options that allow default pages to be displayed when 
    // the current page does not fit into the specified range.
    // If the start level is greater than 0, then work back through ancestors to find
    // the implied root page.
    if (!empty($vars['start_level'])) {
        // FIXME: '+1' only needed if the root page is being hidden. Maybe.
        if ($pagedata['pages'][$pid]['depth'] + (!empty($vars['multi_homed']) ? 1 : 0) < $vars['start_level']) {
            // We are outside the start level.
            // Hide the block if there is no default page to set.
            return;
        } else {
            // We are within a start level.
            // Scan through ancestors, and find the one with the specified level,
            // and add it to the root pids list.
            $scan_pid = $pid;
            while (true) {
                if (empty($pagedata['pages'][$scan_pid]['parent_pid'])) break;
                if ($pagedata['pages'][$scan_pid]['depth'] < $vars['start_level']) {
                    $root_pids[] = $scan_pid;
                    break;
                }
                $scan_pid = $pagedata['pages'][$scan_pid]['parent_pid'];
            }

            // If the root pid has no children, we should hide the block.
            if (!empty($vars['multi_homed']) && empty($pagedata['pages'][$scan_pid]['child_keys'])) return;
        }
    }

    // TODO: handle privileges for pages somewhere. The user/display
    // function handles it for the current page, but there is no
    // point the block providing links to pages that cannot be
    // accessed.

    // Optionally prune branches from the tree.
    // TODO: Make sure we only prune above the root nodes. Trust the user for now to do that.
    //$prune_pids = array(15);
    if (!empty($prune_pids)) {
        foreach($prune_pids as $prune_pid) {
            if (isset($pagedata['pages'][$prune_pid])) {
                // The page exists.
                // Move the current page if necessary.
                if ($pagedata['pages'][$pid]['left'] > $pagedata['pages'][$prune_pid]['left'] && $pagedata['pages'][$pid]['left'] < $pagedata['pages'][$prune_pid]['right']) {
                    // Move the current page down from within the pruned section, to
                    // the current pruning point.
                    $pid = $prune_pid;
                }

                // Reset any of the pruning point's children.
                $pagedata['pages'][$prune_pid]['child_keys'] = array();
                $pagedata['pages'][$prune_pid]['has_children'] = false;
                //var_dump($pagedata);
            }
        }
    }

    // Here we add the various flags to the pagedata, based on
    // the current page.
    $pagedata = xarModAPIfunc(
        'xarpages', 'user', 'addcurrentpageflags',
        array('pagedata' => $pagedata, 'pid' => $pid, 'root_pids' => $root_pids)
    );

    // If not multi-homed, then create a 'root root' page - a virtual page
    // one step back from the displayed root page. This makes the template
    // much easier to implement. The templates need never display the
    // root page passed into them, and always start with the children of
    // that root page.
    if (empty($vars['multi_homed'])) {
        $pagedata['pages'][0] = array(
            'child_keys' => array($pagedata['root_page']['key']),
            'has_children' => true, 'is_ancestor' => true
        );
        unset($pagedata['root_page']);
        $pagedata['root_page'] =& $pagedata['pages'][0];
    }

    // Pass the page data into the block.
    // Merge it in with the existing block details.
    // TODO: It may be quicker to do the merge the other way around?
    $vars = array_merge($vars, $pagedata);

    return $blockinfo;
}

?>

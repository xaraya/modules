<?php
/**
 * File: $Id$
 *
 * Displays a crumb-trail block
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

function xarpages_crumbblock_init()
{
    return array(
        'include_root' => false,
        'root_pids' => array()
    );
}

/**
 * Block info array
 */

function xarpages_crumbblock_info()
{
    return array(
        'text_type' => 'Content',
        'text_type_long' => 'Xarpages Crumbtrail Block',
        'module' => 'xarpages',
        'func_update' => 'xarpages_crumbblock_update',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true,
        'notes' => 'Provides an ancestry trail of the current page in the hierarchy'
    );
}

/**
 * Display func.
 * @param $blockinfo array
 * @returns $blockinfo array
 * @todo Option to display the menu even when not on a relevant page
 */

function xarpages_crumbblock_display($blockinfo)
{
    // Security Check
    // TODO: remove this check once it goes into the blocks centrally.
    //if (!xarSecurityCheck('ViewBlocks', 0, 'Block', 'xarpages:menu:' . $blockinfo['name'])) {return;}

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

    // To start with, we need to know the current page.
    // It could be set (fixed) for the block, passed in
    // via the page cache, or simply not present.
    $pid = 0;

    // Automatic: that means look at the page cache.
    if (xarVarIsCached('Blocks.xarpages', 'current_pid')) {
        $pid = xarVarGetCached('Blocks.xarpages', 'current_pid');
        // Make sure it is numeric.
        if (!isset($pid) || !is_numeric($pid)) {
            $pid = 0;
        }
    }

    // If we don't have a current page, then there is no trail to display.
    if (empty($pid)) {
        return;
    }

    // The page details may have been cached, if
    // we are in the xarpages module, or have several
    // blocks on the same page showing the same tree.
    if (xarVarIsCached('Blocks.xarpages', 'pagedata')) {
        // Pages are cached?
        // The 'serialize' hack ensures we have a proper copy of the
        // paga data, which is a self-referencing array. If we don't
        // do this, then any changes we make will affect the stored version.
        $pagedata = unserialize(serialize(xarVarGetCached('Blocks.xarpages', 'pagedata')));

        // If the cached tree does not contain the current page,
        // then we cannot use it.
        if (!isset($pagedata['pages'][$pid])) {
            $pagedata = array();
        }
    }

    // If there is no pid, then we have no page or tree to display.
    if (empty($pagedata)) {return;}
    
    // If necessary, check whether the current page is under one of the
    // of the allowed root pids.
    if (!empty($root_pids)) {
        if (!xarMod::apiFunc('xarpages', 'user', 'pageintrees', array('pid' => $pid, 'tree_roots' => $root_pids))) {
            return;
        }
    }

    // If we don't have any page data, then there is nothing to display.
    if (empty($pagedata)) {
        return;
    }

    // Here we add the various flags to the pagedata, based on
    // the current page.
    $pagedata = xarMod::apiFunc(
        'xarpages', 'user', 'addcurrentpageflags',
        array('pagedata' => $pagedata, 'pid' => $pid, 'root_pids' => $root_pids)
    );

    // If we don't want to include the root page in the crumbs, then shift it off now.
    if (empty($vars['include_root'])) {
        array_shift($pagedata['ancestors']);
    }

    // We may not have any ancestors left after shifting off the first one.
    if (empty($pagedata['ancestors'])) {
        return;
    }

    // Pass the page data into the block.
    // Merge it in with the existing block details.
    $vars = array_merge($vars, $pagedata);

    return $blockinfo;
}

?>
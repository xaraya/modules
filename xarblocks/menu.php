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
    //    be displayed once that root is reached.
    // 2. Set a 'max depth' value, so only a preset max number of levels
    //    are rendered in a tree.
    // 3. Set behaviour when no current page in the xarpages module is
    //    displayed, e.g. hide menu, show default tree or page etc.

    // Get variables from content block.
    if (!is_array($blockinfo['content'])) {
        $blockinfo['content'] = unserialize($blockinfo['content']);
    }

    // Pointer to simplify referencing.
    $vars =& $blockinfo['content'];

    // The page details will have been cached, providing
    // we are in the xarpages module.
    // TODO: this is just the 'automatic' option - could also
    // provide menus from any page tree.
    if (!xarVarIsCached('Blocks.xarpages', 'pagedata')) {
        return;
    }

    // Pass the cached data into the block.
    $vars = xarVarGetCached('Blocks.xarpages', 'pagedata');

    // Set the root page.
    // TODO: allow the root page to be set at a variety of points.
    // If, for example, the root page is set several levels up the tree,
    // then the menu will remain static and open at just one level, 
    // until a page within that sub-tree is selected.
    if (!empty($vars['ancestors'])) {
        $vars['root_page'] =& reset($vars['ancestors']);
    } else {
        return;
    }

    return $blockinfo;
}

?>
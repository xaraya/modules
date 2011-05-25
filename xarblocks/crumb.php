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
sys::import('xaraya.structures.containers.blocks.basicblock');

class Publications_CrumbBlock extends BasicBlock implements iBlock
{
    public $name                = 'CrumbBlock';
    public $module              = 'publications';
    public $text_type           = 'Crumbtrail';
    public $text_type_long      = 'Publications Crumbtrail Block';
    public $notes               = 'Provides an ancestry trail of the current page in the hierarchy';

    public $include_root        = false;
    public $root_ids           = array();

/**
 * Display func.
 * @param $blockinfo array
 * @returns $blockinfo array
 * @todo Option to display the menu even when not on a relevant page
 */

    function display(Array $data=array())
    {
        $data = parent::display($data);
        if (empty($data)) return;

        $vars = $data['content'];

        if (!empty($vars['root_ids']) && is_array($vars['root_ids'])) {
            $root_ids = $vars['root_ids'];
        } else {
            $root_ids = array();
        }

        // To start with, we need to know the current page.
        // It could be set (fixed) for the block, passed in
        // via the page cache, or simply not present.
        $pid = 0;

        // Automatic: that means look at the page cache.
        if (xarVarIsCached('Blocks.publications', 'current_id')) {
            $id = xarVarGetCached('Blocks.publications', 'current_id');
            // Make sure it is numeric.
            if (!isset($id) || !is_numeric($id)) {$id = 0;}
        }

        // If we don't have a current page, then there is no trail to display.
        if (empty($id)) {return;}

        // The page details may have been cached, if
        // we are in the xarpages module, or have several
        // blocks on the same page showing the same tree.
        if (xarVarIsCached('Blocks.publications', 'pagedata')) {
            // Pages are cached?
            // The 'serialize' hack ensures we have a proper copy of the
            // paga data, which is a self-referencing array. If we don't
            // do this, then any changes we make will affect the stored version.
            $pagedata = unserialize(serialize(xarVarGetCached('Blocks.publications', 'pagedata')));

            // If the cached tree does not contain the current page,
            // then we cannot use it.
            if (!isset($pagedata['pages'][$id])) {$pagedata = array();}
        }

        // If there is no pid, then we have no page or tree to display.
//        if (empty($pagedata)) {return;}

        // If necessary, check whether the current page is under one of the
        // of the allowed root pids.
        if (!empty($root_ids)) {
            if (!xarMod::apiFunc('publications', 'user', 'pageintrees', array('pid' => $id, 'tree_roots' => $root_ids))) {
                return;
            }
        }

        // If we don't have any page data, then there is nothing to display.
        if (empty($pagedata)) { return;}

        // Here we add the various flags to the pagedata, based on
        // the current page.
        $pagedata = xarMod::apiFunc(
            'publications', 'user', 'addcurrentpageflags',
            array('pagedata' => $pagedata, 'id' => $id, 'root_ids' => $root_ids)
        );

        // If we don't want to include the root page in the crumbs, then shift it off now.
        if (empty($vars['include_root'])) {
            array_shift($pagedata['ancestors']);
        }

        // We may not have any ancestors left after shifting off the first one.
        if (empty($pagedata['ancestors'])) {return;}

        // Pass the page data into the block.
        // Merge it in with the existing block details.
        $vars = array_merge($vars, $pagedata);

        $data['content'] = $vars;

        return $data;

    }

}
?>
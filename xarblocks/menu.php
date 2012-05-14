<?php
/**
 * Displays a menu block
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2004-2009 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 * @author Jason Judge
*/

/**
 * init func
 */

    sys::import('xaraya.structures.containers.blocks.basicblock');

    class Publications_MenuBlock extends BasicBlock implements iBlock
    {
        public $name                = 'MenuBlock';
        public $module              = 'publications';
        public $text_type           = 'Content';
        public $text_type_long      = 'Publications Menu Block';
        public $allow_multiple      = true;

        public $form_content        = false;
        public $form_refresh        = false;

        public $show_preview        = true;
        public $multi_homed         = true;
        public $current_source      = 'AUTO'; // Other values: 'DEFAULT'
        public $default_id          = 0; // 0 == 'None'
        public $root_ids            = array();
        public $prune_ids           = array();
        public $max_level           = 0;
        public $start_level         = 0;

        public $func_update         = 'menublock_update';
        public $notes               = "no notes";


/**
 * Display func.
 * @param $blockinfo array
 * @returns $blockinfo array
 */

/**
 * Display func.
 * @param $blockinfo array
 * @returns $blockinfo array
 * @todo Option to display the menu even when not on a relevant page
 */

    function display(Array $data=array())
    {
    // TODO:
    // We want a few facilities:
    // 1. Set a root higher than the real tree root. Pages will only
    //    be displayed once that root is reached. Effectively set one
    //    or more trees, at any depth, that this menu will cover. [DONE]
    // 2. Set a 'max depth' value, so only a preset max number of levels
    //    are rendered in a tree. [DONE]
    // [1 and 2 are a kind of "view window" for levels]
    // 3. Set behaviour when no current page in the Publications module is
    //    displayed, e.g. hide menu, show default tree or page etc. [DONE]
    // 4. Allow the page tree to be pruned at arbitrary specified
    //    pages. That would allow sections of the tree to be pruned
    //    from one menu and added to another (i.e. split menus).
    //    This will also move the current page, if it happens to be in the
    //    pruned section, down to the pruning page. [done]

        $data = $this->getContent();

        // Pointer to simplify referencing.
        $vars =& $data;

        if (!empty($data['root_ids']) && is_array($data['root_ids'])) {
            $root_ids = $data['root_ids'];
        } else {
            $root_ids = array();
        }

        if (!empty($data['prune_ids']) && is_array($data['prune_ids'])) {
            $prune_ids = $data['prune_ids'];
        } else {
            $prune_ids = array();
        }

        // To start with, we need to know the current page.
        // It could be set (fixed) for the block, passed in
        // via the page cache, or simply not present.
        $id = 0;
        if (empty($data['current_source']) || $data['current_source'] == 'AUTO' || $data['current_source'] == 'AUTODEFAULT') {
            // Automatic: that means look at the page cache.
            if (xarVarIsCached('Blocks.publications', 'current_id')) {
                $cached_id = xarVarGetCached('Blocks.publications', 'current_id');
                // Make sure it is numeric.
                if (isset($cached_id) && is_numeric($cached_id)) {
                    $id = $cached_id;
                }
            }
        }

        // Now we may or may not have a page ID.
        // If the page is not set, then check for a default.
        if (empty($id) && !empty($data['default_id'])) {
            // Set the current page to be the default.
            $id = $data['default_id'];
        }

        // The page details *may* have been cached, if
        // we are in the Publications module, or have several
        // blocks on the same page showing the same tree.
        if (xarVarIsCached('Blocks.publications', 'pagedata')) {
            // Pages are cached?
            // The 'serialize' hack ensures we have a proper copy of the
            // paga data, which is a self-referencing array. If we don't
            // do this, then any changes we make will affect the stored version.
            $pagedata = unserialize(serialize(xarVarGetCached('Blocks.publications', 'pagedata')));
            //$pagedata = unserialize(serialize($pagedata));
            // If the cached tree does not contain the current page,
            // then we cannot use it.
            if (!isset($pagedata['pages'][$id])) {
                $pagedata = array();
            }
        }

        // If there is no id, then we have no page or tree to display.
        if (empty($id)) {return;}

        // If necessary, check whether the current page is under one of the
        // of the allowed root ids.
        if (!empty($root_ids)) {
            if (!xarMod::apiFunc('publications', 'user', 'pageintrees', array('id' => $id, 'tree_roots' => $root_ids))) {
                // Not under a root.
                // If the mode is AUTO then leave the menu blank.
                if ($data['current_source'] == 'AUTO' || $data['current_source'] == 'DEFAULT' || empty($data['default_id'])) {
                    return;
                } else {
                    // Use the default page instead.
                    $id = $data['default_id'];
                    $pagedata = array();
                }
            }
        }

        // If we don't have any page data, then fetch it now.
        if (empty($pagedata)) {
            // Get the page data here now.
            $pagedata = xarMod::apiFunc(
                'publications', 'user', 'getmenutree',
                array(
                    'tree_contains_id' => $id,
//                    'dd_flag' => true,
//                    'key' => 'id',
//                    'status' => 'ACTIVE,EMPTY'
                )
            );

            // If $pagedata is empty, then we have an invalid ID or
            // no permissions. Return NULL if so, suppressing the block.
            if (empty($pagedata['pages'])) {return;}

            // Cache the data now we have gone to the trouble of fetching the tree.
            // Only cache it if the cache is empty to start with. We only cache a complete
            // tree here, so if any other blocks need it, it contains all possible
            // pages we could need in that tree.
            if (!xarVarIsCached('Blocks.publications', 'pagedata')) {
                xarVarSetCached('Blocks.publications', 'pagedata', $pagedata);
            }
        }

        // If the user has set a 'start level' then make sure the page sits at that level or above.
        // TODO: take into account the options that allow default pages to be displayed when 
        // the current page does not fit into the specified range.
        // If the start level is greater than 0, then work back through ancestors to find
        // the implied root page.
        if (!empty($data['start_level'])) {
            // FIXME: '+1' only needed if the root page is being hidden. Maybe.
            if ($pagedata['pages'][$id]['depth'] + (!empty($data['multi_homed']) ? 1 : 0) < $data['start_level']) {
                // We are outside the start level.
                // Hide the block if there is no default page to set.
                return;
            } else {
                // We are within a start level.
                // Scan through ancestors, and find the one with the specified level,
                // and add it to the root ids list.
                $scan_id = $id;
                while (true) {
                    if (empty($pagedata['pages'][$scan_id]['parent_id'])) break;
                    if ($pagedata['pages'][$scan_id]['depth'] < $data['start_level']) {
                        $root_ids[] = $scan_id;
                        break;
                    }
                    $scan_id = $pagedata['pages'][$scan_id]['parent_id'];
                }

                // If the root id has no children, we should hide the block.
                if (!empty($data['multi_homed']) && empty($pagedata['pages'][$scan_id]['child_keys'])) return;
            }
        }

        // TODO: handle privileges for pages somewhere. The user/display
        // function handles it for the current page, but there is no
        // point the block providing links to pages that cannot be
        // accessed.

        // Optionally prune branches from the tree.
        // TODO: Make sure we only prune above the root nodes. Trust the user for now to do that.
        //$prune_ids = array(15);
        if (!empty($prune_ids)) {
            foreach($prune_ids as $prune_id) {
                if (isset($pagedata['pages'][$prune_id])) {
                    // The page exists.
                    // Move the current page if necessary.
                    if ($pagedata['pages'][$id]['left'] > $pagedata['pages'][$prune_id]['left'] && $pagedata['pages'][$id]['left'] < $pagedata['pages'][$prune_id]['right']) {
                        // Move the current page down from within the pruned section, to
                        // the current pruning point.
                        $id = $prune_id;
                    }

                    // Reset any of the pruning point's children.
                    $pagedata['pages'][$prune_id]['child_keys'] = array();
                    $pagedata['pages'][$prune_id]['has_children'] = false;
                }
            }
        }

        // transform to the format we need for displaying the menu
        $temp = array();
        foreach ($pagedata['pages'] as $k => $v) $temp[$v['id']] = $v;
        $pagedata['pages'] = $temp;
        
        // Here we add the various flags to the pagedata, based on
        // the current page.
        $pagedata = xarMod::apiFunc(
            'publications', 'user', 'addcurrentpageflags',
            array('pagedata' => $pagedata, 'id' => $id, 'root_ids' => $root_ids)
        );

        // If not multi-homed, then create a 'root root' page - a virtual page
        // one step back from the displayed root page. This makes the template
        // much easier to implement. The templates need never display the
        // root page passed into them, and always start with the children of
        // that root page.
        if (empty($data['multi_homed'])) {
            $pagedata['pages'][0] = array(
                'child_keys' => array($pagedata['root_page']['id']),
                'has_children' => true, 'is_ancestor' => true
            );
            unset($pagedata['root_page']);
            $pagedata['root_page'] =& $pagedata['pages'][0];
        }

        // Pass the page data into the block.
        // Merge it in with the existing block details.
        // TODO: It may be quicker to do the merge the other way around?
        $data = array_merge($data, $pagedata);
                    var_dump($pagedata);

        return $data;
    }
}

?>
<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Jason Judge
 */
/**
 * Displays a crumb-trail block
 *
*/
sys::import('xaraya.structures.containers.blocks.basicblock');

class Publications_CrumbBlock extends BasicBlock implements iBlock
{
    // File Information, supplied by developer, never changes during a versions lifetime, required
    protected $type                = 'crumb';
    protected $module              = 'publications';
    protected $text_type           = 'Crumbtrail';
    protected $text_type_long      = 'Publications Crumbtrail Block';
    protected $notes               = 'Provides an ancestry trail of the current page in the hierarchy';
    // Additional info, supplied by developer, optional 
    protected $type_category    = 'block'; // options [(block)|group] 
    protected $author = '';
    protected $contact = '';
    protected $credits = '';
    protected $license = '';
    
    // blocks subsystem flags
    protected $show_preview = true;  // let the subsystem know if it's ok to show a preview
    // @todo: drop the show_help flag, and go back to checking if help method is declared 
    protected $show_help    = false; // let the subsystem know if this block type has a help() method

    public $include_root        = false;
    public $root_ids           = array();

/**
 * Display func.
 * @param none
 * @returns $data array of template data
 * @todo Option to display the menu even when not on a relevant page
 * @FIXME: if blocks are called before the main module is loaded their values are always empty
 * @FIXME: the calls to cache have no fallbacks and assume module is current main module.
 */

    function display()
    {
        $vars = $this->getContent();

        if (!empty($vars['root_ids']) && is_array($vars['root_ids'])) {
            $root_ids = $vars['root_ids'];
        } else {
            $root_ids = array();
        }

        // To start with, we need to know the current page.
        // It could be set (fixed) for the block, passed in
        // via the page cache, or simply not present.
        $id = 1;

        // Automatic: that means look at the page cache.
        if (xarVarIsCached('Blocks.publications', 'current_id')) {
            $id = xarVarGetCached('Blocks.publications', 'current_id');
            // Make sure it is numeric.
            if (!isset($id) || !is_numeric($id)) {$id = 0;}
        }

        // If we don't have a current page, then there is no trail to display.
        if (empty($id)) {return;}

        // The page details may have been cached, if
        // we have several
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
        $data = array_merge($vars, $pagedata);

        return $data;

    }

}
?>
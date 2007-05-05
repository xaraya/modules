<?php
/**
 *
 * CategoryTree Property
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by to be added
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link to be added
 * @subpackage Categories Module
 * @author Marc Lutolf <mfl@netspan.ch>
 *
 */

sys::import('modules.categories.class.categories');

class CategoryTreeProperty extends DataProperty
{
    public $id         = 30046;
    public $name       = 'categorytree';
    public $desc       = 'CategoryTree';
    public $reqmodules = array('categories');

    function __construct(ObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);

        $this->tplmodule = 'categories';
        $this->filepath   = 'modules/categories/xarproperties';
    }

    public function showInput(Array $data = array())
    {
        if (isset($data['options'])) {
            $this->options = $data['options'];
        } else {
            $this->options = xarModAPIFunc('categories','user','getchildren',array('cid' => 0));
        }

        $trees = array();
        $totalcount = 0;
        foreach ($this->options as $entry) {
            $node = new CategoryTreeNode($entry['cid']);
            $tree = new CategoryTree($node);
            $nodes = $node->depthfirstenumeration();
            $totalcount += $nodes->size();
            $trees[] = $nodes;
        }
        $data['trees'] = $trees;

        // Pager stuff, not working yet
        if(!xarVarFetch('pagerstart',   'isset', $pagerstart,   NULL, XARVAR_DONT_SET)) {return;}
        if(!xarVarFetch('catsperpage',  'isset', $catsperpage,  NULL, XARVAR_DONT_SET)) {return;}
        if (empty($pagerstart)) {
            $data['pagerstart'] = 1;
        } else {
            $data['pagerstart'] = intval($pagerstart);
        }

        if (empty($catsperpage)) {
            $data['catsperpage'] = xarModVars::get('categories','catsperpage');
        } else {
            $data['catsperpage'] = intval($catsperpage);
        }

        $data['pagertotal'] = $totalcount;

        return parent::showInput($data);
    }

}

?>

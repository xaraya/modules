<?php
/**
 *
 * CommentTree Property
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by to be added
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link to be added
 * @subpackage Categories Module
 * @author Marc Lutolf <mfl@netspan.ch>
 *
 */

sys::import('modules.comments.class.comments');

class CommentTreeProperty extends DataProperty
{
    public $id         = 30058;
    public $name       = 'commenttree';
    public $desc       = 'CommentTree';
    public $reqmodules = array('comments');

    function __construct(ObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);

        $this->tplmodule = 'comments';
        $this->filepath   = 'modules/comments/xarproperties';
    }

    public function showInput(Array $data = array())
    {
        if (isset($data['options'])) {
            $this->options = $data['options'];
        } else {
            $this->options = xarModAPIFunc('categories','user','getchildren',array('id' => 0));
        }

        $trees = array();
        $totalcount = 0;
        foreach ($this->options as $entry) {
            $node = new CategoryTreeNode($entry['id']);
            $tree = new CategoryTree($node);
            $nodes = $node->depthfirstenumeration();
            $totalcount += $nodes->size();
            $trees[] = $nodes;
        }
        $data['trees'] = $trees;

        // Pager stuff, perhaps not good to have here
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

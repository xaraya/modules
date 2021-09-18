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
    public $reqmodules = ['comments'];

    public function __construct(ObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);

        $this->tplmodule = 'comments';
        $this->filepath   = 'modules/comments/xarproperties';
    }

    public function showInput(array $data = [])
    {
        if (isset($data['configuration'])) {
            $this->parseConfiguration($data['configuration']);
            unset($data['configuration']);
        }
        extract($data);
        if (!isset($module)) {
            $module = xarMod::getName();
        }
        if (!isset($itemtype)) {
            throw new BadParameterException('itemtype');
        }
        if (!isset($itemid)) {
            throw new BadParameterException('itemid');
        }

        $root = xarMod::apiFunc(
            'comments',
            'user',
            'get_node_root',
            ['modid' => xarMod::getID($module),
                              'objectid' => $itemid,
                              'itemtype' => $itemtype, ]
        );

        // If we don't have one, make one
        if (!count($root)) {
            $cid = xarMod::apiFunc(
                'comments',
                'user',
                'add_rootnode',
                ['modid'    => xarMod::getID($module),
                                        'objectid' => $itemid,
                                        'itemtype' => $itemtype, ]
            );
            if (empty($cid)) {
                throw new Exception('Unable to create root node');
            }
        }
        return xarMod::guiFunc(
            'comments',
            'user',
            'display',
            ['objectid' => $itemid,
                                       'module' => $module,
                                       'itemtype' => $itemtype,
                                       'returnurl' => xarServer::getCurrentURL(), ]
        );
        /*if (isset($data['options'])) {
            $this->options = $data['options'];
        } else {
            $this->options = xarMod::apiFunc('categories','user','getchildren',array('id' => 0));
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
        if(!xarVar::fetch('pagerstart',   'isset', $pagerstart,   NULL, xarVar::DONT_SET)) {return;}
        if(!xarVar::fetch('catsperpage',  'isset', $catsperpage,  NULL, xarVar::DONT_SET)) {return;}
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
        */
        return parent::showInput($data);
    }
}

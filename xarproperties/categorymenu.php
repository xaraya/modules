<?php
/**
 *
 * CategoryMenu Property
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by to be added
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link to be added
 * @subpackage Categories Module
 * @author Marc Lutolf <mfl@netspan.ch>
 *
 */

sys::import('modules.categories.xarproperties.categorytree');

class CategoryMenuProperty extends CategoryTreeProperty
{
    public $id         = 30047;
    public $name       = 'categorymenu';
    public $desc       = 'CategoryMenu';
    public $reqmodules = array('categories');

    public function showInput(Array $data = array())
    {
        if(!xarVarFetch('activetab',    'isset', $data['activetab'],    0, XARVAR_NOT_REQUIRED)) {return;}

        if (!isset($parent)) $parent = 0;
        if (!isset($levels)) $levels = 1;
        // Could also do this using getchildren, although then we get more data we don't really need

        xarMod::loadDbInfo('categories');
        $xartable = xarDB::getTables();
        sys::import('modules.roles.class.xarQuery');
        $q = new xarQuery('SELECT',$xartable['categories']);
        $q->addfield('id');
        $q->addfield('parent_id');
        $q->eq('parent_id',0);
        if (!$q->run()) return;

        $trees = array();
        foreach ($q->output() as $entry) {
            $node = new CategoryTreeNode($entry['id']);
            $tree = new CategoryTree($node);
            $trees[] = $node->breadthfirstenumeration(0);
        }
        $data['tabs'] = $trees;
        return parent::showInput($data);
    }
}

?>

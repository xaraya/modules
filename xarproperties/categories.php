<?php
/**
 * File: $Id$
 *
 * Categories Property
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage dynamicdata properties
 * @author mikespub <mikespub@xaraya.com>
*/
/**
 * Make a &lt;select&gt; box with tree of categories (&#160;&#160;--+ style)
 * e.g. for use in your own admin pages to select root categories for your
 * module, choose a particular subcategory for an item etc.
 *
 *  -- INPUT --
 * @param $args['cid'] optional ID of the root category used for the tree
 *                     (if not specified, the whole tree is shown)
 * @param $args['eid'] optional ID to exclude from the tree (probably not
 *                     very useful in this context)
 * @param $args['multiple'] optional flag (1) to have a multiple select box
 * @param $args['values'] optional array $values[$id] = 1 to mark option $id
 *                        as selected
 * @param $args['return_itself'] include the cid itself (default false)
 * @param $args['select_itself'] allow selecting the cid itself if included (default false)
 * @param $args['show_edit'] show edit link for current selection (default false)
 * @param $args['javascript'] add onchange, onblur or whatever javascript to select (default empty)
 * @param $args['size'] optional size of the select field (default empty)
 * @param $args['name_prefix'] optional prefix for the select field name (default empty)
 *
 *  -- OUTPUT --
 * @returns string
 * @return select box for categories :
 *
 * &lt;select name="cids[]"&gt; (or &lt;select name="cids[]" multiple&gt;)
 * &lt;option value="123"&gt;&#160;&#160;--+&#160;My Cat 123
 * &lt;option value="124" selected&gt;&#160;&#160;&#160;&#160;+&#160;My Cat 123
 * ...
 * &lt;/select&gt;
 *
 *
 *   Options
 *   cids:  bascid:cid[,cid] - select only cids who are descendants of the given basecid(s)
 *   bases: bascid[,bascid] - select only cids who are descendants of the given basecid(s)
 */

sys::import('modules.base.xarproperties.dropdown');
sys::import('modules.categories.xarproperties.categorytree');

class CategoriesProperty extends SelectProperty
{
    public $id         = 100;
    public $name       = 'categories';
    public $desc       = 'Categories';
    public $reqmodules = array('categories');

    public $baselist   = 'all';
    public $cidlist    = array();
    public $itemid     = 0;
    public $showbase   = true;

    function __construct(ObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);
        $this->template  = 'categories';
        $this->tplmodule = 'categories';
        $this->filepath   = 'modules/categories/xarproperties';
    }

    public function checkInput($name = '', $value = null)
    {
        $name = empty($name) ? 'dd_'.$this->id : $name;
        // store the fieldname for validations who need them (e.g. file uploads)
        $this->fieldname = $name;

        // Get the categories from the form
        list($isvalid, $categories) = $this->fetchValue($name . '_categories');
        if ($categories == null) {
            if (!xarVarFetch($name . '_categories', 'array', $categories, array(), XARVAR_NOT_REQUIRED)) return;
        } else {
            if (!is_array($categories)) $categories = array($categories);
        }
        
        // Make sure they are valid
        if (count($categories) > 0) {
            $checkcats= array();
            foreach ($categories as $category) {
                $validcat = xarModAPIFunc('categories','user','getcatinfo',array('cid'=>$category));
                if (!$validcat) {
                    $this->invalid = xarML("The category #(1) is not valid", $category);
                    $this->value = null;
                    return false;
                }
            }
        }
        
        // Get the base categories from the form
        if (!xarVarFetch($name . '_categories_basecats', 'array', $basecats, array(), XARVAR_NOT_REQUIRED)) return;

        // Check their number against the valid categories we have
        if (count($basecats) != count($categories)) {
            $this->invalid = xarML("The number of categories and their base categories is not the same");
            $this->value = null;
            return false;
        }
        return true;
    }

    public function createValue($itemid=0)
    {
        if (!xarVarFetch($name . '_categories_localitemtype', 'int', $itemtype, 0, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch($name . '_categories_localmodule', 'str', $modname, '', XARVAR_NOT_REQUIRED)) return;
        if (empty($modname)) $modname = xarModGetName();
        $info = xarMod::getBaseInfo($modname);
        if (!xarVarFetch($name . '_categories_basecats', 'array', $basecats, array(), XARVAR_NOT_REQUIRED)) return;

        if (!empty($itemid)) {
            $result = xarModAPIFunc('categories', 'admin', 'unlink',
                              array('iid' => $itemid,
                                    'itemtype' => $itemtype,
                                    'modid' => $info['systemid']));
        }

        if (count($categories) > 0) {
            $result = xarModAPIFunc('categories', 'admin', 'linkcat',
                                  array('cids'  => $categories,
                                        'iids'  => array($itemid),
                                        'itemtype' => $itemtype,
                                        'modid' => $info['systemid'],
                                        'basecids'  => $basecats,
                                        'clean_first' => true));

        }

        return true;
    }

    public function updateValue($itemid=0)
    {
        return $this->createValue($itemid);
    }

    public function deleteValue($itemid=0)
    {
        // TODO make this work, but do we need it?
        return $itemid;
    }

    public function returnInput($name = '', $value = null)
    {
        $name = empty($name) ? 'dd_'.$this->id : $name;
        // store the fieldname for validations who need them (e.g. file uploads)
        $this->fieldname = $name;

        list($isvalid, $categories) = $this->fetchValue($name . '_categories');
        if ($isvalid) {
            if (!is_array($categories)) {
                $categories = array($categories);
            } else {
                if (!xarVarFetch($name . '_categories', 'array', $categories, array(), XARVAR_NOT_REQUIRED)) return;
            }
        } else {
            $categories = array();
        }
        return $categories;
    }

    public function saveInput($name = '', $value = null)
    {
        $name = empty($name) ? 'dd_'.$this->id : $name;
        // store the fieldname for validations who need them (e.g. file uploads)
        $this->fieldname = $name;

        if (!xarVarFetch($name . '_categories_localmodule', 'str', $modname, '', XARVAR_NOT_REQUIRED)) return;
        if (empty($modname)) $modname = xarModGetName();
        if (!xarVarFetch($name . '_categories_localitemtype', 'int', $itemtype, 0, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch($name . '_categories_basecats', 'array', $basecats, array(), XARVAR_NOT_REQUIRED)) return;

        $categories = $this->returnInput($name, $value);

        if (!xarVarFetch($name . '_categories_itemid', 'int', $itemid, 0, XARVAR_NOT_REQUIRED)) return;
        if (!$itemid) $itemid = $value;

        $result = xarModAPIFunc('categories', 'admin', 'unlink',
                          array('iid' => $itemid,
                                'itemtype' => $itemtype,
                                'modid' => xarMod::getID($modname)));
        if (count($categories) > 0) {
            $result = xarModAPIFunc('categories', 'admin', 'linkcat',
                                array('cids'  => $categories,
                                      'iids'  => array($itemid),
                                      'itemtype' => $itemtype,
                                      'modid' => xarMod::getID($modname),
                                      'basecids'  => $basecats,
                                      'clean_first' => true));
        }
        return true;
    }

    public function showInput(Array $data = array())
    {
        if (empty($data['module'])) {
            if (!empty($data['localmodule'])) {
                $data['categories_localmodule'] = $data['localmodule'];
            } else {
                $data['categories_localmodule'] = xarModGetName();
            }
        } else {
            $data['categories_localmodule'] = $data['module'];
            unset($data['module']);
        }

        if (!isset($data['itemtype'])) {
            $data['categories_localitemtype'] = 0;
        } else {
            $data['categories_localitemtype'] = $data['itemtype'];
        }

        if (isset($data['validation'])) $this->parseValidation($data['validation']);
        if (!isset($data['bases'])) $data['bases'] = $this->baselist;

        if (!is_array($data['bases'])) {
            // Return an array where each toplevel category is a base category
            if (strtolower($data['bases']) == 'all') {
                if (!isset($data['itemtype'])) {
                    $basecats = xarModAPIFunc('categories','user','getallcatbases',array('module' => $data['categories_localmodule']));
                } else {
                    $basecats = xarModAPIFunc('categories','user','getallcatbases',array('module' => $data['categories_localmodule'], 'itemtype' => $data['categories_localitemtype']));
                }
                $data['basecids'] = array();
                foreach ($basecats as $basecat) $data['basecids'][] = $basecat['category_id'];

            // Return an array where the only base category is the parent all categories
            } elseif (strtolower($data['bases']) == 'single') {
                $data['basecids'] = array(0);

            // Return an array with no base categories
            } elseif (strtolower($data['bases']) == 'none') {
                $data['basecids'] = array();

            // Return an array of base categories we got from the tag
            } else {
                $data['basecids'] = explode(',',$data['bases']);
            }
        } else {
            // still todo: display manually entered basecat trees
            // right now works for 1 basecat
            $data['basecids'] = $data['bases'];
        }

        // sort the base categories
        // TODO: make the sorting changeable
        //sort($data['basecids']);

        $filter = array(
            'getchildren' => true,
            'maxdepth' => isset($data['maxdepth'])?$data['maxdepth']:null,
            'mindepth' => isset($data['mindepth'])?$data['mindepth']:null,
            'cidlist'  => $this->cidlist,
        );
        $returnitself = (empty($data['returnitself'])) ? false : $data['returnitself'];
        $data['trees'] = array();
        if ($data['basecids'] == array(0)) {
            $toplevel = xarModAPIFunc('categories','user','getchildren',array('cid' => 0));
            $nodes = new BasicSet();
            foreach ($toplevel as $entry) {
                $node = new CategoryTreeNode($entry['cid']);
                $node->setfilter($filter);
                $tree = new CategoryTree($node);
                $nodes->addAll($node->depthfirstenumeration());
            }
            $data['trees'][] = $nodes;
        } else {
            foreach ($data['basecids'] as $cid) {
                $nodes = new BasicSet();
                $node = new CategoryTreeNode($cid);
                $node->setfilter($filter);
                $tree = new CategoryTree($node);
                $nodes->addAll($node->depthfirstenumeration());
                $data['trees'][] = $nodes;
            }
        }

        if (!isset($data['name'])) $data['name'] = "dd_" . $this->id;
        if (!isset($data['javascript'])) $data['javascript'] = '';
        if (!isset($data['multiple'])) $data['multiple'] = 0;

        if (empty($data['show_edit']) || !empty($data['multiple'])) {
            $data['show_edit'] = 0;
        }

        if (!empty($data['itemid'])) {
            $data['categories_itemid'] = $data['itemid'];
        } elseif (isset($this->_itemid)) {
            $data['categories_itemid'] = $this->_itemid;
        } else {
            $data['categories_itemid'] = null;
        }

        if (empty($data['value'])) {
            if (empty($this->value)) {

                $links = xarModAPIFunc('categories', 'user', 'getlinkage',
                                       array('itemid' => $data['categories_itemid'],
                                             'itemtype' => $data['categories_localitemtype'],
                                             'module' => $data['categories_localmodule'],
                                              ));
                $catlink = array();
                foreach ($links as $link) $catlink[$link['basecategory']] = $link['category_id'];
                $data['value'] = array();
                foreach ($data['basecids'] as $basecid)
                    $data['value'][] = isset($catlink[$basecid]) ? $catlink[$basecid]: 0;
            } else {
                if (!is_array($this->value)) $this->value = array($this->value);
                $data['value'] = $this->value;
            }
        } elseif (!is_array($data['value'])) {
            $data['value'] = array($data['value'] => $data['value']);
        }

    // Note : $data['values'][$id] will be updated inside the template, so that when several
    //        select boxes are used with overlapping trees, categories will only be selected once
    // This requires that the values are passed by reference : $data['values'] =& $seencids;
//        if (isset($data['values'])) {
//            $GLOBALS['Categories_MakeSelect_Values'] =& $data['values'];
//        }

        return parent::showInput($data);
    }

    public function showOutput(Array $data = array())
    {
        if (empty($data['module'])) {
            if (!empty($data['localmodule'])) {
                $data['categories_localmodule'] = $data['localmodule'];
            } else {
                $data['categories_localmodule'] = xarModGetName();
            }
        } else {
            $data['categories_localmodule'] = $data['module'];
            unset($data['module']);
        }
        if (empty($data['itemtype'])) {
            $data['categories_localitemtype'] = 0;
        } else {
            $data['categories_localitemtype'] = $data['itemtype'];
        }

        if (isset($data['validation'])) $this->parseValidation($data['validation']);
        if (!isset($data['showbase'])) $data['showbase'] = $this->showbase;

        if (!isset($data['name'])) $data['name'] = "dd_" . $this->id;

        if (!empty($data['itemid'])) {
            $data['categories_itemid'] = $data['itemid'];
        } elseif (isset($this->_itemid)) {
            $data['categories_itemid'] = $this->_itemid;
        } else {
            $data['categories_itemid'] = 0;
        }

        if (empty($data['value'])) {
            if (empty($this->value)) {
                $data['value'] = array();
                $links = xarModAPIFunc('categories', 'user', 'getlinkages',
                                       array('items' => array($data['categories_itemid']),
                                             'itemtype' => $data['categories_localitemtype'],
                                             'module' => $data['categories_localmodule'],
                                             ));
                if (!empty($links) && is_array($links) && count($links) > 0) {
                    foreach ($links as $link)
                        foreach ($link as $row) {
                            $data['value'][] = $row;
                        }
                } else {
                    $data['value'] = array();
                }
            } else {
                if (!is_array($this->value)) $this->value = array($this->value);
                $data['value'] = $this->value;
            }
        } elseif (!is_array($data['value'])) {
            $data['value'] = array($data['value'] => $data['value']);
        }
        $temparray = array();
        foreach ($data['value'] as $category) {
            $this->value = $category['category_id'];
            $temparray[] = array_merge($category,array('value' => $this->getOption()));
        }
        $data['value'] = $temparray;
        return parent::showOutput($data);
    }

    function getOption($check = false)
    {
        if (!isset($this->value)) {
             if ($check) return true;
             return null;
        }
        $result = xarModAPIFunc('categories','user','getcatinfo',array('cid' => $this->value));
        if (!empty($result)) {
            if ($check) return true;
            return $result['name'];
        }
        if ($check) return false;
        return $this->value;
    }

}

?>

<?php
/**
 *
 * CategoryPicker Property
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by to be added
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link to be added
 * @subpackage Categories Module
 * @author Marc Lutolf <mfl@netspan.ch>
 *
 */

sys::import('modules.dynamicdata.class.properties.base');

class CategoryPickerProperty extends DataProperty
{
    public $id         = 30050;
    public $name       = 'categorypicker';
    public $desc       = 'CategoryPicker';
    public $reqmodules = array('categories');

    function __construct(ObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);

        $this->filepath   = 'modules/categories/xarproperties';
        $this->tplmodule = 'categories';
    }

    public function checkInput($name = '', $value = null)
    {
        $name = empty($name) ? 'dd_'.$this->id : $name;
        // store the fieldname for validations who need them (e.g. file uploads)
        $this->fieldname = $name;

        if (!xarVarFetch($name . '_categories_numberofbasecats', 'int', $numberofbasecats, 0, XARVAR_NOT_REQUIRED)) return;
        $baseids = array();
        $basecatnames = array();
        $currentbaseids = array();
        if (!xarVarFetch($name . '_categories_basecatcid', 'array', $basecid, array(), XARVAR_DONT_REUSE)) return;
        if (!xarVarFetch($name . '_categories_basecatname', 'array', $basename, array(), XARVAR_DONT_REUSE)) return;
        if (!xarVarFetch($name . '_categories_basecatitemtype', 'array', $baseitemtype, array(), XARVAR_DONT_REUSE)) return;
        if (!xarVarFetch($name . '_categorypicker_localmodule', 'str', $localmodule, xarModGetName(), XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch($name . '_categorypicker_localitemtype', 'int', $localitemtype, 0, XARVAR_NOT_REQUIRED)) return;
        xarMod::loadDbInfo('categories');
        $xartable = xarDB::getTables();
        
        // Remove all the entries for this module
        $q = new xarQuery('DELETE', $xartable['categories_basecategories']);
        $q->eq('module_id',xarMod::getID($localmodule));
        if (!$q->run()) return;

        for($i=0;$i<$numberofbasecats;$i++) {
            $thiscid = isset($basecid[$i]) && is_numeric($basecid[$i]) ? $basecid[$i] : 0;
            $thisname = (isset($basename[$i]) && !empty($basename[$i])) ? $basename[$i] : xarML('Base Category #(1)',$i+1);
            $thisitemtype = isset($baseitemtype[$i]) ? $baseitemtype[$i] : $localitemtype;
            $thisbasecat = xarModAPIFunc('categories','user','getcatbase',array('name' => $thisname, 'module' => $localmodule, 'itemtype' => $thisitemtype));
            $q = new xarQuery('INSERT', $xartable['categories_basecategories']);
            $q->addfield('module_id',xarMod::getID($localmodule));
            $q->addfield('itemtype',$thisitemtype);
            $q->addfield('name',$thisname);
            $q->addfield('category_id',$thiscid);
            if (!$q->run()) return;
        }
        return true;
    }

    public function showInput(Array $data = array())
    {
        if (empty($data['module'])) {
            $data['categories_localmodule'] = xarModGetName();
        } else {
            $data['categories_localmodule'] = $data['module'];
            unset($data['module']);
        }
        if (empty($data['itemtype'])) {
            $data['categories_localitemtype'] = 0;
        } else {
            $data['categories_localitemtype'] = $data['itemtype'];
        }

        if (!isset($data['basecids'])) {
            if (empty($data['categories_localitemtype'])) {
                $basecats = xarModAPIFunc('categories','user','getallcatbases',array('module' => $data['categories_localmodule']));
            } else {
                $basecats = xarModAPIFunc('categories','user','getallcatbases',array('module' => $data['categories_localmodule'], 'itemtype' => $data['categories_localitemtype']));
            }
        }
        if (!isset($data['categories_numberofbasecats'])) $data['categories_numberofbasecats'] = count($basecats);
        $seencid = array();
        $items = array();
        for ($i = 0; $i < $data['categories_numberofbasecats']; $i++) {
            $item = array();
            $item['num'] = $i;
            $item['category_id'] = isset($basecats[$i]['category_id']) ? $basecats[$i]['category_id']: 0;
            $item['name'] = isset($basecats[$i]['name']) ? $basecats[$i]['name']: xarML('Base Category #(1)',$i);
            $item['itemtype'] = isset($basecats[$i]['itemtype']) ? $basecats[$i]['itemtype']: 0;
            // preserve order of root categories if possible - do not use this for multi-select !
            if (isset($cleancids[$i])) $seencid = array($cleancids[$i] => 1);
            // TODO: improve memory usage
            $items[] = $item;
        }
        unset($item);

        if(xarSecurityCheck('AddCategories',0)) {
            $newcat = xarML('new');
        } else {
            $newcat = '';
        }

        $data['newcat'] = $newcat;
        $data['items'] = $items;
        $data['module'] = 'categories';
        return parent::showInput($data);
    }

    public function showOutput(Array $data = array())
    {
        if (empty($data['firstline'])) {
            $data['firstline'] = '';
        }
        if (empty($data['value'])) {
            $data['value'] = array();
        }
        if (empty($data['module'])) {
            $data['localmodule'] = xarModGetName();
        }

        if (empty($data['itemtype'])) {
            $data['itemtype'] = 0;
        }

        $basecidlist = unserialize(xarModVars::get($data['localmodule'],'basecids',$data['itemtype']));
        if (!isset($data['basecids'])) $data['basecids'] = $basecidlist;
        if (!isset($data['numberofbasecats'])) $data['numberofbasecats'] = count($data['basecids']);
        if (!is_array($data['value'])) {
            $msg = xarML('The value passed to the categorypicker property is not an array');
            throw new BadParameterException(null,$msg);
        }

        $seencid = array();
        $items = array();
        for ($i = 0; $i < $data['numberofbasecats']; $i++) {
            $item = array();
            $item['num'] = $i;
            $item['category_id'] = isset($basecidlist[$i]) ? $basecidlist[$i]: 0;
            $item['value'] = isset($data['value'][$i]) ? $data['value'][$i]: 0;
            // preserve order of root categories if possible - do not use this for multi-select !
            if (isset($cleancids[$i])) $seencid = array($cleancids[$i] => 1);
            // TODO: improve memory usage
            $items[] = $item;
        }
        unset($item);

        $data['items'] = $items;
        $data['module'] = 'categories';

        return parent::showOutput($data);
    }
}

?>

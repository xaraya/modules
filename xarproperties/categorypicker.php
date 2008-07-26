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
        if (!xarVarFetch($name . '_categorypicker_localmodule', 'str', $localmodule, xarModGetName(), XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch($name . '_categorypicker_localitemtype', 'str', $localitemtype, 0, XARVAR_NOT_REQUIRED)) return;
        $info = xarMod::getBaseInfo($localmodule);

        xarMod::loadDbInfo('categories');
        $xartable = xarDB::getTables();
        for($i=0;$i<$numberofbasecats;$i++) {
            $thiscid = isset($basecid[$i]) ? $basecid[$i] : 0;
            $thisname = (isset($basename[$i]) && !empty($basename[$i])) ? $basename[$i] : xarML('Base Category #(1)',$i+1);
            $thisbasecat = xarModAPIFunc('categories','user','getcatbase',array('name' => $thisname, 'module' => $name));
            if (!empty($thisbasecat)) {
                $currentbaseids[] = $thisbasecat['id'];
                $q = new xarQuery('UPDATE', $xartable['categories_basecategories']);
                $q->eq('module_id',$info['systemid']);
                $q->eq('name',$thisname);
                $q->addfield('category_id',$thiscid);
                if (!$q->run()) return;
            } else {
                $q = new xarQuery('INSERT', $xartable['categories_basecategories']);
                $q->addfield('module_id',$info['systemid']);
                $q->addfield('itemtype',$localitemtype);
                $q->addfield('name',$thisname);
                $q->addfield('category_id',$thiscid);
                if (!$q->run()) return;
                $currentbaseids[] = $q->lastid($xartable['categories_basecategories'], 'id');
            }
        }
        $q = new xarQuery('DELETE', $xartable['categories_basecategories']);
        $q->eq('module_id',$info['systemid']);
        if (!empty($localitemtype)) $q->eq('itemtype',$localitemtype);
        if (!empty($currentbaseids)) $q->notin('id',$currentbaseids);
        if (!$q->run()) return;
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
/*        $modid = xarModGetIDFromName($data['categories_localmodule']);
        if (empty($modid)) {
            $msg = xarML('Invalid #(1) for #(2) method #(3)() in module #(4)', 'module name', 'property', 'showinput', 'categories');
            throw new BadParameterException(null,$msg);
        }
*/
        if (!isset($data['basecids'])) {
            $basecats = xarModAPIFunc('categories','user','getallcatbases',array('module' => $data['categories_localmodule'], 'itemtype' => $data['categories_localitemtype']));
        }
        if (!isset($data['categories_numberofbasecats'])) $data['categories_numberofbasecats'] = count($basecats);
/*        $categories_basecatcid = array();
        $categories_basecatname = array();
        foreach ($basecats as $basecat) {
            $categories_basecatcid[] = $basecat['cid'];
            $categories_basecatname[] = $basecat['name'];
        }
        $data['categories_basecatcid'] = $categories_basecatcid;
        $data['categories_basecatname'] = $categories_basecatname;
        */
        $seencid = array();
        $items = array();
        for ($i = 0; $i < $data['categories_numberofbasecats']; $i++) {
            $item = array();
            $item['num'] = $i;
            $item['category_id'] = isset($basecats[$i]['category_id']) ? $basecats[$i]['category_id']: 0;
            $item['name'] = isset($basecats[$i]['name']) ? $basecats[$i]['name']: xarML('Base Category #(1)',$i);
            // preserve order of root categories if possible - do not use this for multi-select !
            if (isset($cleancids[$i])) $seencid = array($cleancids[$i] => 1);
            // TODO: improve memory usage
            // limit to some reasonable depth for now
            /*
            $item['select'] = xarModAPIFunc('categories', 'visual', 'makeselect',
                                           array('values' => &$seencid,
                                                 'name_prefix' => 'config_',
                                                 'maximum_depth' => 4,
                                                 'show_edit' => true));*/
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
/*        $modid = xarModGetIDFromName($data['localmodule']);
        if (empty($modid)) {
            $msg = xarML('Invalid #(1) for #(2) method #(3)() in module #(4)', 'module name', 'property', 'showinput', 'categories');
            throw new BadParameterException(null,$msg);
        }
*/
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
            // limit to some reasonable depth for now
            /*
            $item['select'] = xarModAPIFunc('categories', 'visual', 'makeselect',
                                           array('values' => &$seencid,
                                                 'name_prefix' => 'config_',
                                                 'maximum_depth' => 4,
                                                 'show_edit' => true));*/
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

        return parent::showOutput($data);
    }
/*    public function checkInput($name = '', $value = null)
    {
        $name = empty($name) ? 'dd_'.$this->id : $name;
        // store the fieldname for validations who need them (e.g. file uploads)
        $this->fieldname = $name;
        if (!isset($value)) {
            list($isvalid, $years) = $this->fetchValue($name . '_year');
            list($isvalid, $months) = $this->fetchValue($name . '_month');
            list($isvalid, $days) = $this->fetchValue($name . '_day');
//                if (!xarVarFetch($name . '_year', 'isset', $years,  NULL, XARVAR_DONT_SET)) {return;}
//                if (!xarVarFetch($name . '_month', 'isset', $months,  NULL, XARVAR_DONT_SET)) {return;}
//                if (!xarVarFetch($name . '_day', 'isset', $days,  NULL, XARVAR_DONT_SET)) {return;}
        }
        if (!isset($years) ||!isset($months) ||!isset($days)) return false;
        $value = mktime(0,0,0,$months,$days,$years);
    if (!xarVarFetch('numberofbasecats', 'int', $data['numberofbasecats'], 0, XARVAR_NOT_REQUIRED)) return;
    $count = $data['numberofbasecats'];
    $basecids = array();
    if (!xarVarFetch('basecid', 'array', $basecid, '', XARVAR_DONT_REUSE)) return;
    for($i=0;$i<$count;$i++) $basecids[] = isset($basecid[$i]) ? $basecid[$i] : 0;
    if (!empty($ptid)) {
        xarModSetUserVar('articles','basecids',serialize($basecids),$ptid);
    } else {
        xarModVars::set('articles','basecids',serialize($basecids));
    }


        return $this->validateValue($value);
    }
    */
}

?>

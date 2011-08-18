<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * 
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * Update configuration
 */

sys::import('modules.dynamicdata.class.properties.master');

function publications_admin_updateconfig()
{
    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;
    // Get parameters
    //A lot of these probably are bools, still might there be a need to change the template to return
    //'true' and 'false' to use those...
    if(!xarVarFetch('settings',          'array',   $settings,      array(), XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('usetitleforurl',    'isset', $usetitleforurl,    0,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('defaultstate',     'isset', $defaultstate,     0,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('defaultsort',       'isset', $defaultsort,  'date',  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('usealias',          'isset', $usealias,          0,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('ptid',              'isset', $ptid,              xarModVars::get('publications', 'defaultpubtype'),  XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'global', XARVAR_NOT_REQUIRED)) return;

    if (!xarSecurityCheck('AdminPublications',1,'Publication',"$ptid:All:All:All")) return;

    if ($data['tab'] == 'global') {
        if(!xarVarFetch('defaultpubtype',    'isset', $defaultpubtype,    1,  XARVAR_NOT_REQUIRED)) {return;}
        if(!xarVarFetch('sortpubtypes',      'isset', $sortpubtypes,   'id',  XARVAR_NOT_REQUIRED)) {return;}
        if (!xarVarFetch('defaultlanguage', 'str:1:100', $defaultlanguage, xarModVars::get('publications', 'defaultlanguage'), XARVAR_NOT_REQUIRED)) return;

        xarModVars::set('publications', 'defaultpubtype', $defaultpubtype);
        xarModVars::set('publications', 'sortpubtypes', $sortpubtypes);
        xarModVars::set('publications', 'defaultlanguage', $defaultlanguage);

        // Get the special pages.
        foreach(array('defaultpage', 'errorpage', 'notfoundpage', 'noprivspage') as $special_name) {
            unset($special_id);
            if (!xarVarFetch($special_name, 'id', $special_id, 0, XARVAR_NOT_REQUIRED)) {return;}
            xarModVars::set('publications', $special_name, $special_id);
        }

        if (xarDB::getType() == 'mysql') {
            if (!xarVarFetch('fulltext', 'isset', $fulltext, '', XARVAR_NOT_REQUIRED)) {return;}
            $oldval = xarModVars::get('publications', 'fulltextsearch');
            $index = 'i_' . xarDB::getPrefix() . '_publications_fulltext';
            if (empty($fulltext) && !empty($oldval)) {
                // Get database setup
                $dbconn = xarDB::getConn();
                $xartable = xarDB::getTables();
                $publicationstable = $xartable['publications'];
                // Drop fulltext index on publications table
                $query = "ALTER TABLE $publicationstable DROP INDEX $index";
                $result =& $dbconn->Execute($query);
                if (!$result) return;
                xarModVars::set('publications', 'fulltextsearch', '');
            } elseif (!empty($fulltext) && empty($oldval)) {
                //$searchfields = array('title','summary','body','notes');
                $searchfields = explode(',',$fulltext);
                // Get database setup
                $dbconn = xarDB::getConn();
                $xartable = xarDB::getTables();
                $publicationstable = $xartable['publications'];
                // Add fulltext index on publications table
                $query = "ALTER TABLE $publicationstable ADD FULLTEXT $index (" . join(', ', $searchfields) . ")";
                $result =& $dbconn->Execute($query);
                if (!$result) return;
                xarModVars::set('publications', 'fulltextsearch', join(',',$searchfields));
            }
        }
        
        // Module settings
        $data['module_settings'] = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'publications'));
        $data['module_settings']->setFieldList('items_per_page, use_module_alias, module_alias_name, enable_short_urls, user_menu_link', 'use_module_icons');
        $isvalid = $data['module_settings']->checkInput();
        if (!$isvalid) {
            return xarTplModule('base','admin','modifyconfig', $data);
        } else {
            $itemid = $data['module_settings']->updateItem();
        }

        // Pull the base category ids from the template and save them
        $picker = DataPropertyMaster::getProperty(array('name' => 'categorypicker'));
        $picker->checkInput('basecid');
    } else {

        // Get the publication type for this display and save the settings to it
        $pubtypeobject = DataObjectMaster::getObject(array('name' => 'publications_types'));
        $pubtypeobject->getItem(array('itemid' => $ptid));
        $configsettings = $pubtypeobject->properties['configuration']->getValue();

        $checkbox = DataPropertyMaster::getProperty(array('name' => 'checkbox'));
        $boxes = array(
                        'show_hitount',
                        'show_ratings',
                        'show_keywords',
                        'show_comments',
                        'show_prevnext',
                        'show_archives',
                        'show_publinks',
                        'show_pubcount',
                        'show_map',
                        'prevnextart',
                        'dot_transform',
                        'title_transform',
                        'show_categories',
                        'show_catcount',
                        'show_prevnext',
                       );
        foreach ($boxes as $box) {
            $isvalid = $checkbox->checkInput($box);
            if ($isvalid) $settings[$box] = $checkbox->value;
        }

//        foreach ($configsettings as $key => $value)
//            if (!isset($settings[$key])) $settings[$key] = 0;

        $isvalid = true;
        
        // Get the default access rules
        $access = DataPropertyMaster::getProperty(array('name' => 'access'));
        $validprop = $access->checkInput("access_add");
        $addaccess = $access->value;
        $isvalid = $isvalid && $validprop;
        $validprop = $access->checkInput("access_display");
        $displayaccess = $access->value;
        $isvalid = $isvalid && $validprop;
        $validprop = $access->checkInput("access_modify");
        $modifyaccess = $access->value;
        $isvalid = $isvalid && $validprop;
        $validprop = $access->checkInput("access_delete");
        $deleteaccess = $access->value;
        $isvalid = $isvalid && $validprop;
        $allaccess = array(
            'add' => $addaccess,
            'display' => $displayaccess,
            'modify' => $modifyaccess,
            'delete' => $deleteaccess,
        );
        $pubtypeobject->properties['access']->setValue(serialize($allaccess));
        $pubtypeobject->properties['configuration']->setValue(serialize($settings));
        $pubtypeobject->updateItem(array('itemid' => $ptid));

        $pubtypes = xarModAPIFunc('publications','user','get_pubtypes');
        if ($usealias) {
            xarModSetAlias($pubtypes[$ptid]['name'],'publications');
        } else {
            xarModDelAlias($pubtypes[$ptid]['name'],'publications');
        }

    }
    xarController::redirect(xarModURL('publications', 'admin', 'modifyconfig',
                                  array('ptid' => $ptid, 'tab' => $data['tab'])));
    return true;
}
?>
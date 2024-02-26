<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author mikespub
 */
/**
 * Update configuration
 */

sys::import('modules.dynamicdata.class.properties.master');

function publications_admin_updateconfig()
{
    if (!xarSecurity::check('AdminPublications')) return;

    // Confirm authorisation code
    if (!xarSec::confirmAuthKey()) return;
    // Get parameters
    //A lot of these probably are bools, still might there be a need to change the template to return
    //'true' and 'false' to use those...
    if(!xarVar::fetch('settings',          'array',   $settings,        array(), xarVar::NOT_REQUIRED)) {return;}
    if(!xarVar::fetch('usetitleforurl',    'int',     $usetitleforurl,  xarModVars::get('publications', 'usetitleforurl'),  xarVar::NOT_REQUIRED)) {return;}
    if(!xarVar::fetch('defaultstate',      'isset',   $defaultstate,    0,  xarVar::NOT_REQUIRED)) {return;}
    if(!xarVar::fetch('defaultsort',       'isset',   $defaultsort,     'date',  xarVar::NOT_REQUIRED)) {return;}
    if(!xarVar::fetch('usealias',          'int',     $usealias,        0,  xarVar::NOT_REQUIRED)) {return;}
    if(!xarVar::fetch('ptid',              'isset',   $ptid,            xarModVars::get('publications', 'defaultpubtype'),  xarVar::NOT_REQUIRED)) {return;}
    if (!xarVar::fetch('multilanguage',    'int',     $multilanguage,   0, xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('tab',              'str:1:10',$data['tab'],     'global', xarVar::NOT_REQUIRED)) return;

    if (!xarSecurity::check('AdminPublications',1,'Publication',"$ptid:All:All:All")) return;

    if ($data['tab'] == 'global') {
        if(!xarVar::fetch('defaultpubtype',      'isset', $defaultpubtype,    1,  xarVar::NOT_REQUIRED)) {return;}
        if(!xarVar::fetch('sortpubtypes',        'isset', $sortpubtypes,   'id',  xarVar::NOT_REQUIRED)) {return;}
        if (!xarVar::fetch('defaultlanguage',    'str:1:100', $defaultlanguage, xarModVars::get('publications', 'defaultlanguage'), xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('debugmode',          'checkbox', $debugmode, xarModVars::get('publications', 'debugmode'), xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('use_process_states', 'checkbox', $use_process_states, xarModVars::get('publications', 'use_process_states'), xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('use_versions',       'checkbox', $use_versions, xarModVars::get('publications', 'use_versions'), xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('hide_tree_display',  'checkbox', $hide_tree_display, xarModVars::get('publications', 'hide_tree_display'), xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('admin_override',     'int', $admin_override, xarModVars::get('publications', 'admin_override'), xarVar::NOT_REQUIRED)) return;

        xarModVars::set('publications', 'defaultpubtype', $defaultpubtype);
        xarModVars::set('publications', 'sortpubtypes', $sortpubtypes);
        xarModVars::set('publications', 'defaultlanguage', $defaultlanguage);
        xarModVars::set('publications', 'debugmode', $debugmode);
        xarModVars::set('publications', 'usealias', $usealias);
        xarModVars::set('publications', 'usetitleforurl', $usetitleforurl);
        xarModVars::set('publications', 'use_process_states',$use_process_states);
        xarModVars::set('publications', 'use_versions',$use_versions);
        xarModVars::set('publications', 'hide_tree_display',$hide_tree_display);
        xarModVars::set('publications', 'admin_override',$admin_override);
        
        // Allow multilanguage only if the languages property is present
        sys::import('modules.dynamicdata.class.properties.registration');
        $types = PropertyRegistration::Retrieve();
        if (isset($types[30039])) {
            xarModVars::set('publications', 'multilanguage', $multilanguage);
        } else {
            xarModVars::set('publications', 'multilanguage', 0);
        }

        // Get the special pages.
        foreach(array('defaultpage', 'errorpage', 'notfoundpage', 'noprivspage') as $special_name) {
            unset($special_id);
            if (!xarVar::fetch($special_name, 'id', $special_id, 0, xarVar::NOT_REQUIRED)) {return;}
            xarModVars::set('publications', $special_name, $special_id);
        }

        if (xarDB::getType() == 'mysql') {
            if (!xarVar::fetch('fulltext', 'isset', $fulltext, '', xarVar::NOT_REQUIRED)) {return;}
            $oldval = xarModVars::get('publications', 'fulltextsearch');
            $index = 'i_' . xarDB::getPrefix() . '_publications_fulltext';
            if (empty($fulltext) && !empty($oldval)) {
                // Get database setup
                $dbconn = xarDB::getConn();
                $xartable = xarDB::getTables();
                $publicationstable = $xartable['publications'];
                // Drop fulltext index on publications table
                $query = "ALTER TABLE $publicationstable DROP INDEX $index";
                $result = $dbconn->Execute($query);
                if (!$result) return;
                xarModVars::set('publications', 'fulltextsearch', '');
            } elseif (!empty($fulltext) && empty($oldval)) {
                $searchfields = array('title','description','summary','body1');
//                $searchfields = explode(',',$fulltext);
                // Get database setup
                $dbconn = xarDB::getConn();
                $xartable = xarDB::getTables();
                $publicationstable = $xartable['publications'];
                // Add fulltext index on publications table
                $query = "ALTER TABLE $publicationstable ADD FULLTEXT $index (" . join(', ', $searchfields) . ")";
                $result = $dbconn->Execute($query);
                if (!$result) return;
                xarModVars::set('publications', 'fulltextsearch', join(',',$searchfields));
            }
        }
        
        // Module settings
        $data['module_settings'] = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'publications'));
        $data['module_settings']->setFieldList('items_per_page, use_module_alias, module_alias_name, enable_short_urls, user_menu_link, use_module_icons, frontend_page, backend_page');
        $isvalid = $data['module_settings']->checkInput();
        if (!$isvalid) {
            return xarTpl::module('base','admin','modifyconfig', $data);
        } else {
            $itemid = $data['module_settings']->updateItem();
        }

        // Pull the base category ids from the template and save them
        $picker = DataPropertyMaster::getProperty(array('name' => 'categorypicker'));
        $picker->checkInput('basecid');
    } elseif ($data['tab'] == 'pubtypes') {

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
                        'dot_transform',
                        'title_transform',
                        'show_categories',
                        'show_catcount',
                       );
        foreach ($boxes as $box) {
            $isvalid = $checkbox->checkInput($box);
            if ($isvalid) $settings[$box] = $checkbox->value;
        }

        $isvalid = true;

        foreach ($_POST as $name => $field) {
            if (strpos($name, 'custom_') === 0) {
                $settings[$name] = $field;
            }
        }
        
        $pubtypeobject->properties['configuration']->setValue(serialize($settings));
        $pubtypeobject->updateItem(array('itemid' => $ptid));

        $pubtypes = xarMod::apiFunc('publications','user','get_pubtypes');
        if ($usealias) {
            xarModAlias::set($pubtypes[$ptid]['name'],'publications');
        } else {
            xarModAlias::delete($pubtypes[$ptid]['name'],'publications');
        }

    } elseif ($data['tab'] == 'redirects') {
        $redirects = DataPropertyMaster::getProperty(array('name' => 'array'));
        $redirects->display_column_definition['value'] = array(array("From","To"),array(2,2),array("",""),array("",""));  
        $isvalid = $redirects->checkInput("redirects");
        xarModVars::set('publications','redirects',$redirects->value);
    }
    xarController::redirect(xarController::URL('publications', 'admin', 'modifyconfig',
                                  array('ptid' => $ptid, 'tab' => $data['tab'])));
    return true;
}
?>
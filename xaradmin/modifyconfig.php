<?php
/**
 * Sitemapper Module
 *
 * @package modules
 * @subpackage sitemapper module
 * @category Third Party Xaraya Module
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Main configuration page for the sitemapper module
 *
 */

function sitemapper_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurity::check('AdminSitemapper')) return;
    if (!xarVar::fetch('phase', 'str:1:100', $phase, 'modify', xarVar::NOT_REQUIRED, xarVar::PREP_FOR_DISPLAY)) return;
    if (!xarVar::fetch('tab', 'str:1:100', $data['tab'], 'general', xarVar::NOT_REQUIRED)) return;

    $data['module_settings'] = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'sitemapper'));
    $data['module_settings']->setFieldList('items_per_page,, use_module_alias, use_module_icons');
    $data['module_settings']->getItem();

    sys::import('modules.dynamicdata.class.objects.master');
    $engines = DataObjectMaster::getObjectList(array('name' => 'sitemapper_engines'));
    $data['engines'] = $engines->getItems(array('where' => 'state = 3'));
    $modules_available = xarMod::apiFunc('modules', 'admin', 'getitems');
    
    // Resort so that we get the regids in the checkboxlist
    $data['modules_available'] = array();
    foreach ($modules_available as $row) $data['modules_available'][] = array('id' => $row['regid'], 'name' => $row['name']);
    
    switch (strtolower($phase)) {
        case 'modify':
        default:
            switch ($data['tab']) {
                case 'general':
                    break;
                case 'tab2':
                    break;
                case 'tab3':
                    break;
                default:
                    break;
            }

            break;

        case 'update':
            // Confirm authorisation code
            if (!xarSec::confirmAuthKey()) {
                return xarTpl::module('privileges','user','errors',array('layout' => 'bad_author'));
            }        
            switch ($data['tab']) {
                case 'general':
                    $isvalid = $data['module_settings']->checkInput();
                    if (!$isvalid) {
                        return xarTpl::module('dynamicdata','admin','modifyconfig', $data);        
                    } else {
                        $itemid = $data['module_settings']->updateItem();
                    }

                    if (!xarVar::fetch('file_create_xml', 'int', $file_create_xml, 0, xarVar::NOT_REQUIRED)) return;
                    if (!xarVar::fetch('file_create_zip', 'int', $file_create_zip, 0, xarVar::NOT_REQUIRED)) return;
                    if (!xarVar::fetch('file_xml_filename', 'str:1:', $xml_filename, 0, xarVar::NOT_REQUIRED)) return;
                    if (!xarVar::fetch('file_zip_filename', 'str:1:', $zip_filename, 0, xarVar::NOT_REQUIRED)) return;

                    xarModVars::set('sitemapper', 'file_create_xml', $file_create_xml);
                    xarModVars::set('sitemapper', 'file_create_zip', $file_create_zip);
                    xarModVars::set('sitemapper', 'xml_filename', $xml_filename);
                    xarModVars::set('sitemapper', 'zip_filename', $zip_filename);

                    // Get a chekcbox list sys::import('modules.dynamicdata.class.properties.master');
                    $checkbox_list = DataPropertyMaster::getProperty(array('name' => 'checkboxlist'));
                    
                    // Get the value for the engines to submit to
                    $checkbox_list->checkInput('submit_engines');
                    xarModVars::set('sitemapper', 'submit_engines', $checkbox_list->value);
            
                    // Get the value for the engines to submit to
                    $checkbox_list->checkInput('modules_to_map');
                    xarModVars::set('sitemapper', 'modules_to_map', $checkbox_list->value);
                    break;
                case 'tab2':
                    break;
                case 'tab3':
                    break;
                default:
                    break;
            }
            break;
    }
    $data['submit_engines'] = xarModVars::get('sitemapper', 'submit_engines');
    return $data;
}
?>
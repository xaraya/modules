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
        if (!xarSecurityCheck('AdminSitemapper')) return;
        if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
        if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'general', XARVAR_NOT_REQUIRED)) return;

        $data['module_settings'] = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'sitemapper'));
        $data['module_settings']->setFieldList('items_per_page,, use_module_alias, use_module_icons');
        $data['module_settings']->getItem();

        sys::import('modules.dynamicdata.class.objects.master');
        $engines = DataObjectMaster::getObjectList(array('name' => 'sitemapper_engines'));
        $data['engines'] = $engines->getItems(array('where' => 'state = 3'));
        
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
                if (!xarSecConfirmAuthKey()) {
                    return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
                }        
                switch ($data['tab']) {
                    case 'general':
                        $isvalid = $data['module_settings']->checkInput();
                        if (!$isvalid) {
                            return xarTplModule('dynamicdata','admin','modifyconfig', $data);        
                        } else {
                            $itemid = $data['module_settings']->updateItem();
                        }

                        if (!xarVarFetch('file_create_xml', 'int', $file_create_xml, 0, XARVAR_NOT_REQUIRED)) return;
                        if (!xarVarFetch('file_create_zip', 'int', $file_create_zip, 0, XARVAR_NOT_REQUIRED)) return;
                        if (!xarVarFetch('file_xml_filename', 'str:1:', $xml_filename, 0, XARVAR_NOT_REQUIRED)) return;
                        if (!xarVarFetch('file_zip_filename', 'str:1:', $zip_filename, 0, XARVAR_NOT_REQUIRED)) return;

                        xarModVars::set('sitemapper', 'file_create_xml', $file_create_xml);
                        xarModVars::set('sitemapper', 'file_create_zip', $file_create_zip);
                        xarModVars::set('sitemapper', 'xml_filename', $xml_filename);
                        xarModVars::set('sitemapper', 'zip_filename', $zip_filename);

                        sys::import('modules.dynamicdata.class.properties.master');
                        $submit_engines = DataPropertyMaster::getProperty(array('name' => 'checkboxlist'));
                        $submit_engines->checkInput('submit_engines');
                        xarModVars::set('sitemapper', 'submit_engines', $submit_engines->value);
                
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

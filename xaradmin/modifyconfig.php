<?php
/**
 * EAV Module
 *
 * @package modules
 * @subpackage eav
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2013 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Main configuration page for the eav module
 *
 */

// Use this version of the modifyconfig file when the module is not a  utility module

    function eav_admin_modifyconfig()
    {
        // Security Check
        if (!xarSecurity::check('AdminEAV')) return;
        if (!xarVar::fetch('phase', 'str:1:100', $phase, 'modify', xarVar::NOT_REQUIRED, xarVar::PREP_FOR_DISPLAY)) return;
        if (!xarVar::fetch('tab', 'str:1:100', $data['tab'], 'general', xarVar::NOT_REQUIRED)) return;

        $data['module_settings'] = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'eav'));
        $data['module_settings']->setFieldList('items_per_page, use_module_alias, madule_alias_name, use_module_icons, enable_short_urls');
        $data['module_settings']->getItem();

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
                if (!xarSec::confirmAuthKey()) return;
                switch ($data['tab']) {
                    case 'general':
                        $isvalid = $data['module_settings']->checkInput();
                        if (!$isvalid) {
                            return xarTpl::module('eav','admin','modifyconfig', $data);        
                        } else {
                            $itemid = $data['module_settings']->updateItem();
                        }
                        break;
                    case 'tab2':
                        break;
                    case 'tab3':
                        break;
                    default:
                        break;
                }

                xarController::redirect(xarController::URL('eav', 'admin', 'modifyconfig',array('tab' => $data['tab'])));
                // Return
                return true;
                break;

        }
        $data['authid'] = xarSec::genAuthKey();
        return $data;
    }
?>

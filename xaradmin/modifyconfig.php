<?php
/**
 * Logconfig Module
 *
 * @package modules
 * @subpackage logconfig module
 * @copyright (C) 2010 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Main configuration page for the logconfig module
 *
 */

// Use this version of the modifyconfig file when the module is not a  utility module

    function logconfig_admin_modifyconfig()
    {
        // Security Check
        if (!xarSecurity::check('AdminLogconfig')) return;
        if (!xarVar::fetch('phase', 'str:1:100', $phase, 'modify', xarVar::NOT_REQUIRED, xarVar::PREP_FOR_DISPLAY)) return;
        if (!xarVar::fetch('tab', 'str:1:100', $data['tab'], 'general', xarVar::NOT_REQUIRED)) return;

        $data['module_settings'] = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'logconfig'));
        $data['module_settings']->setFieldList('items_per_page, use_module_alias, module_alias_name, enable_short_urls');
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
                    /* Remove this for now
                        $isvalid = $data['module_settings']->checkInput();
                        if (!$isvalid) {
                            return xarTpl::module('logconfig','admin','modifyconfig', $data);        
                        } else {
                            $itemid = $data['module_settings']->updateItem();
                        }
					*/
						// The overall switch to enable logging
						if (!xarVar::fetch('logenabled', 'int', $logenabled, 0, xarVar::NOT_REQUIRED)) return;
					
						// Update the config.system file
						$variables = array('Log.Enabled' => $logenabled);
						xarMod::apiFunc('installer','admin','modifysystemvars', array('variables' => $variables));
                                                
                        xarController::redirect(xarController::URL('logconfig', 'admin', 'modifyconfig', array('tab' => 'general')));
                        break;
                    case 'tab2':
                        break;
                    case 'tab3':
                        break;
                    default:
                        break;
                }

                xarResponse::redirect(xarController::URL('logconfig', 'admin', 'modifyconfig',array('tab' => $data['tab'])));
                // Return
                return true;
                break;

        }
        $data['authid'] = xarSec::genAuthKey();
        return $data;
    }
?>

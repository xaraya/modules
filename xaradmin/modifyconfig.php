<?php
/**
 * Main configuration page for the xarayatesting module
 *
 */

// Use this version of the modifyconfig file when the module is not a utility module

    function xarayatesting_admin_modifyconfig()
    {
        // Security Check
        if (!xarSecurity::check('AdminXarayatesting')) {
            return;
        }
        if (!xarVar::fetch('phase', 'str:1:100', $phase, 'modify', xarVar::NOT_REQUIRED, xarVar::PREP_FOR_DISPLAY)) {
            return;
        }
        if (!xarVar::fetch('tab', 'str:1:100', $data['tab'], 'general', xarVar::NOT_REQUIRED)) {
            return;
        }

        $data['module_settings'] = xarMod::apiFunc('base', 'admin', 'getmodulesettings', ['module' => 'xarayatesting']);
        $data['module_settings']->setFieldList('items_per_page,');
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
                if (!xarSec::confirmAuthKey()) {
                    return xarTpl::module('privileges', 'user', 'errors', ['layout' => 'bad_author']);
                }
                switch ($data['tab']) {
                    case 'general':
                        $isvalid = $data['module_settings']->checkInput();
                        if (!$isvalid) {
                            return xarTpl::module('dynamicdata', 'admin', 'modifyconfig', $data);
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
                break;
        }
        return $data;
    }

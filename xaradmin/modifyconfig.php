<?php
/**
 * Realms Module
 *
 * @package modules
 * @subpackage realms module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

/**
 * Main configuration page for the realms module
 *
 */


function realms_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurity::check('AdminRealms')) {
        return;
    }
    if (!xarVar::fetch('phase', 'str:1:100', $phase, 'modify', xarVar::NOT_REQUIRED, xarVar::PREP_FOR_DISPLAY)) {
        return;
    }
    if (!xarVar::fetch('tab', 'str:1:100', $data['tab'], 'general', xarVar::NOT_REQUIRED)) {
        return;
    }

    $data['module_settings'] = xarMod::apiFunc('base', 'admin', 'getmodulesettings', ['module' => 'realms']);
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
            if (!xarSec::confirmAuthKey()) {
                return;
            }
            switch ($data['tab']) {
                case 'general':
                    $isvalid = $data['module_settings']->checkInput();
                    if (!$isvalid) {
                        return xarTpl::module('realms', 'admin', 'modifyconfig', $data);
                    } else {
                        $itemid = $data['module_settings']->updateItem();
                    }

                    if (!xarVar::fetch('link_role', 'checkbox', $link_role, false, xarVar::NOT_REQUIRED)) {
                        return;
                    }
                    if (!xarVar::fetch('default_realm', 'int', $default_realm, 0, xarVar::NOT_REQUIRED)) {
                        return;
                    }

                    xarModVars::set('realms', 'link_role', $link_role);
                    xarModVars::set('realms', 'default_realm', $default_realm);
                    break;
                case 'tab2':
                    break;
                case 'tab3':
                    break;
                default:
                    break;
            }

            xarController::redirect(xarController::URL('realms', 'admin', 'modifyconfig', ['tab' => $data['tab']]));
            // Return
            return true;
            break;
    }
    $data['authid'] = xarSec::genAuthKey();
    return $data;
}

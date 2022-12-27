<?php
/**
 * Otp Module
 *
 * @package modules
 * @subpackage otp
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2017 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Main configuration page for the otp object
 *
 */

// Use this version of the modifyconfig file when creating utility modules

function otp_admin_modifyconfig_utility()
{
    // Security Check
    if (!xarSecurity::check('AdminOtp')) {
        return;
    }
    if (!xarVar::fetch('phase', 'str:1:100', $phase, 'modify', xarVar::NOT_REQUIRED, xarVar::PREP_FOR_DISPLAY)) {
        return;
    }
    if (!xarVar::fetch('tab', 'str:1:100', $data['tab'], 'otp_general', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('tabmodule', 'str:1:100', $tabmodule, 'otp', xarVar::NOT_REQUIRED)) {
        return;
    }
    $hooks = xarModHooks::call('module', 'getconfig', 'otp');
    if (!empty($hooks) && isset($hooks['tabs'])) {
        foreach ($hooks['tabs'] as $key => $row) {
            $configarea[$key]  = $row['configarea'];
            $configtitle[$key] = $row['configtitle'];
            $configcontent[$key] = $row['configcontent'];
        }
        array_multisort($configtitle, SORT_ASC, $hooks['tabs']);
    } else {
        $hooks['tabs'] = [];
    }

    $regid = xarMod::getRegID($tabmodule);
    switch (strtolower($phase)) {
        case 'modify':
        default:
            switch ($data['tab']) {
                case 'otp_general':
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
            if (!xarVar::fetch('items_per_page', 'int', $items_per_page, xarModVars::get('otp', 'items_per_page'), xarVar::NOT_REQUIRED, xarVar::PREP_FOR_DISPLAY)) {
                return;
            }
            if (!xarVar::fetch('shorturls', 'checkbox', $shorturls, false, xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('modulealias', 'checkbox', $use_module_alias, xarModVars::get('otp', 'use_module_alias'), xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('module_alias_name', 'str', $module_alias_name, xarModVars::get('otp', 'module_alias_name'), xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('defaultmastertable', 'str', $defaultmastertable, xarModVars::get('otp', 'defaultmastertable'), xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('bar', 'str:1', $bar, 'Bar', xarVar::NOT_REQUIRED)) {
                return;
            }

            $modvars = [
                            'defaultmastertable',
                            'bar',
                            ];

            if ($data['tab'] == 'otp_general') {
                xarModVars::set('otp', 'items_per_page', $items_per_page);
                xarModVars::set('otp', 'supportshorturls', $shorturls);
                xarModVars::set('otp', 'use_module_alias', $use_module_alias);
                xarModVars::set('otp', 'module_alias_name', $module_alias_name);
                foreach ($modvars as $var) {
                    if (isset($$var)) {
                        xarModVars::set('otp', $var, $$var);
                    }
                }
            }
            foreach ($modvars as $var) {
                if (isset($$var)) {
                    xarModItemVars::set('otp', $var, $$var, $regid);
                }
            }

            xarController::redirect(xarController::URL('otp', 'admin', 'modifyconfig', ['tabmodule' => $tabmodule, 'tab' => $data['tab']]));
            // Return
            return true;
            break;
    }
    $data['hooks'] = $hooks;
    $data['tabmodule'] = $tabmodule;
    $data['authid'] = xarSec::genAuthKey();
    return $data;
}

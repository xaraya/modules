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
 * Main configuration page for the eav object
 *
 */

// Use this version of the modifyconfig file when creating utility modules

function eav_admin_modifyconfig_utility()
{
    // Security Check
    if (!xarSecurity::check('AdminEAV')) {
        return;
    }
    if (!xarVar::fetch('phase', 'str:1:100', $phase, 'modify', xarVar::NOT_REQUIRED, xarVar::PREP_FOR_DISPLAY)) {
        return;
    }
    if (!xarVar::fetch('tab', 'str:1:100', $data['tab'], 'eav_general', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('tabmodule', 'str:1:100', $tabmodule, 'eav', xarVar::NOT_REQUIRED)) {
        return;
    }
    $hooks = xarModHooks::call('module', 'getconfig', 'eav');
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
                case 'eav_general':
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
            if (!xarVar::fetch('items_per_page', 'int', $items_per_page, xarModVars::get('eav', 'items_per_page'), xarVar::NOT_REQUIRED, xarVar::PREP_FOR_DISPLAY)) {
                return;
            }
            if (!xarVar::fetch('shorturls', 'checkbox', $shorturls, false, xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('modulealias', 'checkbox', $use_module_alias, xarModVars::get('eav', 'use_module_alias'), xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('module_alias_name', 'str', $module_alias_name, xarModVars::get('eav', 'module_alias_name'), xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('defaultmastertable', 'str', $defaultmastertable, xarModVars::get('eav', 'defaultmastertable'), xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('bar', 'str:1', $bar, 'Bar', xarVar::NOT_REQUIRED)) {
                return;
            }

            $modvars = [
                            'defaultmastertable',
                            'bar',
                            ];

            if ($data['tab'] == 'eav_general') {
                xarModVars::set('eav', 'items_per_page', $items_per_page);
                xarModVars::set('eav', 'supportshorturls', $shorturls);
                xarModVars::set('eav', 'use_module_alias', $use_module_alias);
                xarModVars::set('eav', 'module_alias_name', $module_alias_name);
                foreach ($modvars as $var) {
                    if (isset($$var)) {
                        xarModVars::set('eav', $var, $$var);
                    }
                }
            }
            foreach ($modvars as $var) {
                if (isset($$var)) {
                    xarModItemVars::set('eav', $var, $$var, $regid);
                }
            }

            xarController::redirect(xarController::URL('eav', 'admin', 'modifyconfig', ['tabmodule' => $tabmodule, 'tab' => $data['tab']]));
            // Return
            return true;
            break;
    }
    $data['hooks'] = $hooks;
    $data['tabmodule'] = $tabmodule;
    $data['authid'] = xarSec::genAuthKey();
    return $data;
}

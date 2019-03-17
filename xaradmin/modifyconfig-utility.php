<?php
/**
 * Reminders Module
 *
 * @package modules
 * @subpackage reminders
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2019 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Main configuration page for the reminders object
 *
 */

// Use this version of the modifyconfig file when creating utility modules

function reminders_admin_modifyconfig_utility()
{
    // Security Check
    if (!xarSecurityCheck('AdminReminders')) return;
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'reminders_general', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tabmodule', 'str:1:100', $tabmodule, 'reminders', XARVAR_NOT_REQUIRED)) return;
    $hooks = xarModCallHooks('module', 'getconfig', 'reminders');
    if (!empty($hooks) && isset($hooks['tabs'])) {
        foreach ($hooks['tabs'] as $key => $row) {
            $configarea[$key]  = $row['configarea'];
            $configtitle[$key] = $row['configtitle'];
            $configcontent[$key] = $row['configcontent'];
        }
        array_multisort($configtitle, SORT_ASC, $hooks['tabs']);
    } else {
        $hooks['tabs'] = array();
    }

    $regid = xarMod::getRegID($tabmodule);
    switch (strtolower($phase)) {
        case 'modify':
        default:
            switch ($data['tab']) {
                case 'reminders_general':
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
            if (!xarSecConfirmAuthKey()) return;
            if (!xarVarFetch('items_per_page', 'int', $items_per_page, xarModVars::get('reminders', 'items_per_page'), XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
            if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('modulealias', 'checkbox', $use_module_alias,  xarModVars::get('reminders', 'use_module_alias'), XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('module_alias_name', 'str', $module_alias_name,  xarModVars::get('reminders', 'module_alias_name'), XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('defaultmastertable',    'str',      $defaultmastertable, xarModVars::get('reminders', 'defaultmastertable'), XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('bar', 'str:1', $bar, 'Bar', XARVAR_NOT_REQUIRED)) return;

            $modvars = array(
                            'defaultmastertable',
                            'bar',
                            );

            if ($data['tab'] == 'reminders_general') {
                xarModVars::set('reminders', 'items_per_page', $items_per_page);
                xarModVars::set('reminders', 'supportshorturls', $shorturls);
                xarModVars::set('reminders', 'use_module_alias', $use_module_alias);
                xarModVars::set('reminders', 'module_alias_name', $module_alias_name);
                foreach ($modvars as $var) if (isset($$var)) xarModVars::set('reminders', $var, $$var);
            }
            foreach ($modvars as $var) if (isset($$var)) xarModItemVars::set('reminders', $var, $$var, $regid);

            xarController::redirect(xarModURL('reminders', 'admin', 'modifyconfig',array('tabmodule' => $tabmodule, 'tab' => $data['tab'])));
            // Return
            return true;
            break;

    }
    $data['hooks'] = $hooks;
    $data['tabmodule'] = $tabmodule;
    $data['authid'] = xarSecGenAuthKey();
    return $data;
}
?>

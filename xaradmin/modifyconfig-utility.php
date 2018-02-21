<?php
/**
 * Cacher Module
 *
 * @package modules
 * @subpackage cacher
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2014 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Main configuration page for the cacher object
 *
 */

// Use this version of the modifyconfig file when creating utility modules

function cacher_admin_modifyconfig_utility()
{
    // Security Check
    if (!xarSecurityCheck('AdminCacher')) return;
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'cacher_general', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tabmodule', 'str:1:100', $tabmodule, 'cacher', XARVAR_NOT_REQUIRED)) return;
    $hooks = xarModCallHooks('module', 'getconfig', 'cacher');
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
                case 'cacher_general':
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
            if (!xarVarFetch('items_per_page', 'int', $items_per_page, xarModVars::get('cacher', 'items_per_page'), XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
            if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('modulealias', 'checkbox', $use_module_alias,  xarModVars::get('cacher', 'use_module_alias'), XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('module_alias_name', 'str', $module_alias_name,  xarModVars::get('cacher', 'module_alias_name'), XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('defaultmastertable',    'str',      $defaultmastertable, xarModVars::get('cacher', 'defaultmastertable'), XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('bar', 'str:1', $bar, 'Bar', XARVAR_NOT_REQUIRED)) return;

            $modvars = array(
                            'defaultmastertable',
                            'bar',
                            );

            if ($data['tab'] == 'cacher_general') {
                xarModVars::set('cacher', 'items_per_page', $items_per_page);
                xarModVars::set('cacher', 'supportshorturls', $shorturls);
                xarModVars::set('cacher', 'use_module_alias', $use_module_alias);
                xarModVars::set('cacher', 'module_alias_name', $module_alias_name);
                foreach ($modvars as $var) if (isset($$var)) xarModVars::set('cacher', $var, $$var);
            }
            foreach ($modvars as $var) if (isset($$var)) xarModItemVars::set('cacher', $var, $$var, $regid);

            xarController::redirect(xarModURL('cacher', 'admin', 'modifyconfig',array('tabmodule' => $tabmodule, 'tab' => $data['tab'])));
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

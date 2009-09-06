<?php

/**
 * modify configuration
 */
    function categories_admin_modifyconfig()
    {
        // Security Check
        if (!xarSecurityCheck('AdminCategories')) return;
        if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
        if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'categories_general', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('tabmodule', 'str:1:100', $tabmodule, 'categories', XARVAR_NOT_REQUIRED)) return;
        $hooks = xarModCallHooks('module', 'getconfig', 'categories');
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
                    case 'categories_general':
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
                if (!xarVarFetch('items_per_page', 'int', $items_per_page, xarModVars::get('categories', 'items_per_page'), XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
                if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('modulealias', 'checkbox', $use_module_alias,  xarModVars::get('categories', 'use_module_alias'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('module_alias_name', 'str', $module_alias_name,  xarModVars::get('categories', 'module_alias_name'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('usejsdisplay', 'checkbox', $usejsdisplay, xarModVars::get('categories', 'usejsdisplay'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('numstats', 'int', $numstats, xarModVars::get('categories', 'numstats'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('showtitle', 'checkbox', $showtitle, xarModVars::get('categories', 'showtitle'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('allowbatch', 'checkbox', $allowbatch, xarModVars::get('categories', 'allowbatch'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('categoriesobject', 'str', $categoriesobject, xarModVars::get('categories', 'categoriesobject'), XARVAR_NOT_REQUIRED)) return;

                $modvars = array(
                                'usejsdisplay',
                                'numstats',
                                'showtitle',
                                'allowbatch',
                                'categoriesobject',
                                );

                if ($data['tab'] == 'categories_general') {
                    xarModVars::set('categories', 'items_per_page', $items_per_page);
                    xarModVars::set('categories', 'enable_short_urls', $shorturls);
                    xarModVars::set('categories', 'use_module_alias', $use_module_alias);
                    xarModVars::set('categories', 'module_alias_name', $module_alias_name);
                    foreach ($modvars as $var) if (isset($$var)) xarModVars::set('categories', $var, $$var);
                }
                foreach ($modvars as $var) if (isset($$var)) xarModItemVars::set('categories', $var, $$var, $regid);

                xarResponse::Redirect(xarModURL('categories', 'admin', 'modifyconfig',array('tabmodule' => $tabmodule, 'tab' => $data['tab'])));
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

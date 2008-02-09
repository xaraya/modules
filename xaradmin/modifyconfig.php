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

        $regid = xarModGetIDFromName($tabmodule);
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
                if (!xarVarFetch('itemsperpage', 'int', $itemsperpage, xarModVars::get('categories', 'itemsperpage'), XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
                if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('modulealias', 'checkbox', $useModuleAlias,  xarModVars::get('categories', 'useModuleAlias'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('aliasname', 'str', $aliasname,  xarModVars::get('categories', 'aliasname'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('usejsdisplay', 'checkbox', $usejsdisplay, xarModVars::get('categories', 'usejsdisplay'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('numstats', 'int', $numstats, xarModVars::get('categories', 'numstats'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('showtitle', 'checkbox', $showtitle, xarModVars::get('categories', 'showtitle'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('allowbatch', 'checkbox', $allowbatch, xarModVars::get('categories', 'allowbatch'), XARVAR_NOT_REQUIRED)) return;

                $modvars = array(
                                'usejsdisplay',
                                'numstats',
                                'showtitle',
                                'allowbatch',
                                );

                if ($data['tab'] == 'categories_general') {
                    xarModVars::set('categories', 'itemsperpage', $itemsperpage);
                    xarModVars::set('categories', 'supportshorturls', $shorturls);
                    xarModVars::set('categories', 'useModuleAlias', $useModuleAlias);
                    xarModVars::set('categories', 'aliasname', $aliasname);
                    foreach ($modvars as $var) if (isset($$var)) xarModVars::set('categories', $var, $$var);
                }
                foreach ($modvars as $var) if (isset($$var)) xarModItemVars::set('categories', $var, $$var, $regid);

                xarResponseRedirect(xarModURL('categories', 'admin', 'modifyconfig',array('tabmodule' => $tabmodule, 'tab' => $data['tab'])));
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

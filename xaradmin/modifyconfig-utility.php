<?php
/**
 * Main configuration page for the ckeditor object
 *
 */

// Use this version of the modifyconfig file when creating utility modules

    function ckeditor_admin_modifyconfig()
    {
        // Security Check
        if (!xarSecurityCheck('AdminCKEditor')) return;
        if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
        if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'ckeditor_general', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('tabmodule', 'str:1:100', $tabmodule, 'ckeditor', XARVAR_NOT_REQUIRED)) return;
        $hooks = xarModCallHooks('module', 'getconfig', 'ckeditor');
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
                    case 'ckeditor_general':
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
                if (!xarVarFetch('itemsperpage', 'int', $itemsperpage, xarModVars::get('ckeditor', 'itemsperpage'), XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
                if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('modulealias', 'checkbox', $useModuleAlias,  xarModVars::get('ckeditor', 'useModuleAlias'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('aliasname', 'str', $aliasname,  xarModVars::get('ckeditor', 'aliasname'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('defaultmastertable',    'str',      $defaultmastertable, xarModVars::get('ckeditor', 'defaultmastertable'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('bar', 'str:1', $bar, 'Bar', XARVAR_NOT_REQUIRED)) return;

                $modvars = array(
                                'defaultmastertable',
                                'bar',
                                );

                if ($data['tab'] == 'ckeditor_general') {
                    xarModVars::set('ckeditor', 'itemsperpage', $itemsperpage);
                    xarModVars::set('ckeditor', 'supportshorturls', $shorturls);
                    xarModVars::set('ckeditor', 'useModuleAlias', $useModuleAlias);
                    xarModVars::set('ckeditor', 'aliasname', $aliasname);
                    foreach ($modvars as $var) if (isset($$var)) xarModVars::set('ckeditor', $var, $$var);
                }
                foreach ($modvars as $var) if (isset($$var)) xarModItemVars::set('ckeditor', $var, $$var, $regid);

                xarResponseRedirect(xarModURL('ckeditor', 'admin', 'modifyconfig',array('tabmodule' => $tabmodule, 'tab' => $data['tab'])));
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

<?php
/**
 * Main configuration page for the foo module
 *
 */

// Use this version of the modifyconfig file when the module is not a  utility module

    function foo_admin_modifyconfig()
    {
        // Security Check
        if (!xarSecurityCheck('AdminFoo')) return;
        if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
        if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'general', XARVAR_NOT_REQUIRED)) return;
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
                if (!xarSecConfirmAuthKey()) return;
                switch ($data['tab']) {
                    case 'general':
                        if (!xarVarFetch('itemsperpage', 'int', $itemsperpage, xarModVars::get('foo', 'itemsperpage'), XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
                        if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
                        if (!xarVarFetch('modulealias', 'checkbox', $useModuleAlias,  xarModVars::get('foo', 'useModuleAlias'), XARVAR_NOT_REQUIRED)) return;
                        if (!xarVarFetch('aliasname', 'str', $aliasname,  xarModVars::get('foo', 'aliasname'), XARVAR_NOT_REQUIRED)) return;

                        xarModVars::set('foo', 'itemsperpage', $itemsperpage);
                        xarModVars::set('foo', 'SupportShortURLs', $shorturls);
                        xarModVars::set('foo', 'useModuleAlias', $useModuleAlias);
                        xarModVars::set('foo', 'aliasname', $aliasname);
                        break;
                    case 'tab2':
                        break;
                    case 'tab3':
                        break;
                    default:
                        break;
                }

                xarResponse::Redirect(xarModURL('foo', 'admin', 'modifyconfig',array('tab' => $data['tab'])));
                // Return
                return true;
                break;

        }
        $data['authid'] = xarSecGenAuthKey();
        return $data;
    }
?>

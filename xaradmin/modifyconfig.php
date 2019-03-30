<?php
/**
 * Karma Module
 *
 * @package modules
 * @subpackage karma
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2019 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Main configuration page for the karma module
 *
 */

// Use this version of the modifyconfig file when the module is not a  utility module

    function karma_admin_modifyconfig()
    {
        // Security Check
        if (!xarSecurityCheck('AdminKarma')) return;
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
                        if (!xarVarFetch('itemsperpage', 'int', $itemsperpage, xarModVars::get('karma', 'itemsperpage'), XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
                        if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
                        if (!xarVarFetch('modulealias', 'checkbox', $useModuleAlias,  xarModVars::get('karma', 'useModuleAlias'), XARVAR_NOT_REQUIRED)) return;
                        if (!xarVarFetch('aliasname', 'str', $aliasname,  xarModVars::get('karma', 'aliasname'), XARVAR_NOT_REQUIRED)) return;

                        xarModVars::set('karma', 'itemsperpage', $itemsperpage);
                        xarModVars::set('karma', 'SupportShortURLs', $shorturls);
                        xarModVars::set('karma', 'useModuleAlias', $useModuleAlias);
                        xarModVars::set('karma', 'aliasname', $aliasname);
                        break;
                    case 'tab2':
                        break;
                    case 'tab3':
                        break;
                    default:
                        break;
                }

                xarResponse::redirect(xarModURL('karma', 'admin', 'modifyconfig',array('tab' => $data['tab'])));
                // Return
                return true;
                break;

        }
        $data['authid'] = xarSecGenAuthKey();
        return $data;
    }
?>

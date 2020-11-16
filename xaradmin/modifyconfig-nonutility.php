<?php
/**
 * Payments Module
 *
 * @package modules
 * @subpackage payments
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2016 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Main configuration page for the payments object
 *
 */

// Use this version of the modifyconfig file when the module is not a  utility module

    function payments_admin_modifyconfig()
    {
        // Security Check
        if (!xarSecurity::check('AdminPayments')) {
            return;
        }
        if (!xarVar::fetch('phase', 'str:1:100', $phase, 'modify', xarVar::NOT_REQUIRED, xarVar::PREP_FOR_DISPLAY)) {
            return;
        }
        if (!xarVar::fetch('tab', 'str:1:100', $data['tab'], 'general', xarVar::NOT_REQUIRED)) {
            return;
        }
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
                        if (!xarVar::fetch('itemsperpage', 'int', $itemsperpage, xarModVars::get('payments', 'itemsperpage'), xarVar::NOT_REQUIRED, xarVar::PREP_FOR_DISPLAY)) {
                            return;
                        }
                        if (!xarVar::fetch('shorturls', 'checkbox', $shorturls, false, xarVar::NOT_REQUIRED)) {
                            return;
                        }
                        if (!xarVar::fetch('modulealias', 'checkbox', $useModuleAlias, xarModVars::get('payments', 'useModuleAlias'), xarVar::NOT_REQUIRED)) {
                            return;
                        }
                        if (!xarVar::fetch('aliasname', 'str', $aliasname, xarModVars::get('payments', 'aliasname'), xarVar::NOT_REQUIRED)) {
                            return;
                        }

                        xarModVars::set('payments', 'itemsperpage', $itemsperpage);
                        xarModVars::set('payments', 'SupportShortURLs', $shorturls);
                        xarModVars::set('payments', 'useModuleAlias', $useModuleAlias);
                        xarModVars::set('payments', 'aliasname', $aliasname);
                        break;
                    case 'tab2':
                        break;
                    case 'tab3':
                        break;
                    default:
                        break;
                }

                xarController::redirect(xarController::URL('payments', 'admin', 'modifyconfig', array('tab' => $data['tab'])));
                // Return
                return true;
                break;

        }
        $data['authid'] = xarSec::genAuthKey();
        return $data;
    }

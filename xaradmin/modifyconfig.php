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

function payments_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurity::check('AdminPayments')) {
        return;
    }
    if (!xarVar::fetch('phase', 'str:1:100', $phase, 'modify', xarVar::NOT_REQUIRED, xarVar::PREP_FOR_DISPLAY)) {
        return;
    }
    if (!xarVar::fetch('tab', 'str:1:100', $data['tab'], 'payments_general', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('tabmodule', 'str:1:100', $tabmodule, 'payments', xarVar::NOT_REQUIRED)) {
        return;
    }

    /*sys::import('xaraya.structures.hooks.observer');
    $subject = new HookSubject('payments');
    $messenger = $subject->getMessenger();
    $messenger->setHook('module', 'getconfig');

    $hooks = $subject->notify();
    if (!empty($hooks) && isset($hooks['tabs'])) {
        foreach ($hooks['tabs'] as $key => $row) {
            $configarea[$key]  = $row['configarea'];
            $configtitle[$key] = $row['configtitle'];
            $configcontent[$key] = $row['configcontent'];
        }
        array_multisort($configtitle, SORT_ASC, $hooks['tabs']);
    } else {
        $hooks['tabs'] = array();
    }*/

    $data['module_settings'] = xarMod::apiFunc('base', 'admin', 'getmodulesettings', ['module' => 'payments']);
    $data['module_settings']->setFieldList('items_per_page, use_module_alias, enable_short_urls, use_module_icons, frontend_page, backend_page');
    $data['module_settings']->getItem();

    $regid = xarMod::getRegID($tabmodule);
    switch (strtolower($phase)) {
        case 'modify':
        default:
            switch ($data['tab']) {
                case 'payments_general':
                default:
                    $object = DataObjectMaster::getObjectList(['name' => 'payments_gateways']);
                    $data['items'] = $object->getItems(['where' => 'state eq 3']);
                    break;
            }

            break;

        case 'update':
            // Confirm authorisation code
            if (!xarSec::confirmAuthKey()) {
                return;
            }
            if (!xarVar::fetch('customerobject', 'str', $customerobject, xarModVars::get('payments', 'customerobject'), xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('orderobject', 'str', $orderobject, xarModVars::get('payments', 'orderobject'), xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('gateway', 'int', $gateway, xarModVars::get('payments', 'gateway'), xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('runpayments', 'checkbox', $runpayments, xarModVars::get('payments', 'runpayments'), xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('savetodb', 'checkbox', $savetodb, xarModVars::get('payments', 'savetodb'), xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('alertemail', 'checkbox', $alertemail, xarModVars::get('payments', 'alertemail'), xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('alertemailaddr', 'str', $alertemailaddr, xarModVars::get('payments', 'alertemailaddr'), xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('defaultcurrency', 'str', $defaultcurrency, xarModVars::get('payments', 'defaultcurrency'), xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('defaultamount', 'float', $defaultamount, xarModVars::get('payments', 'defaultamount'), xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('process', 'int', $process, xarModVars::get('payments', 'process'), xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('allowanonpay', 'checkbox', $allowanonpay, xarModVars::get('payments', 'allowanonpay'), xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('payments_active', 'checkbox', $payments_active, xarModVars::get('payments', 'payments_active'), xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('message_id', 'int', $message_id, xarModVars::get('payments', 'message_id'), xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('message_prefix', 'str', $message_prefix, xarModVars::get('payments', 'message_prefix'), xarVar::NOT_REQUIRED)) {
                return;
            }

            $modvars = [
                            'customerobject',
                            'orderobject',
                            'gateway',
                            'runpayments',
                            'savetodb',
                            'alertemail',
                            'alertemailaddr',
                            'defaultcurrency',
                            'defaultamount',
                            'process',
                            'allowanonpay',
                            'payments_active',
                            'message_id',
                            'message_prefix',
                            ];

            if ($data['tab'] == 'payments_general') {
                $isvalid = $data['module_settings']->checkInput();
                if (!$isvalid) {
                    return xarTpl::module('payments', 'admin', 'modifyconfig', $data);
                } else {
                    $itemid = $data['module_settings']->updateItem();
                }

                foreach ($modvars as $var) {
                    if (isset($$var)) {
                        xarModVars::set('payments', $var, $$var);
                    }
                }
            }
            foreach ($modvars as $var) {
                if (isset($$var)) {
                    xarModItemVars::set('payments', $var, $$var, $regid);
                }
            }

            if (!xarVar::fetch('enable_demomode', 'int', $enable_demomode, 0, xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('demousers', 'str', $demousers, '', xarVar::NOT_REQUIRED)) {
                return;
            }
            $demousers = explode(',', $demousers);
            $validdemousers = [];
            foreach ($demousers as $demouser) {
                if (empty($demouser)) {
                    continue;
                }
                $user = xarMod::apiFunc('roles', 'user', 'get', ['uname' => trim($demouser)]);
                if (!empty($user)) {
                    $validdemousers[$user['uname']] = $user['uname'];
                }
            }
            xarModVars::set('payments', 'enable_demomode', $enable_demomode);
            xarModVars::set('payments', 'demousers', serialize($validdemousers));

            xarController::redirect(xarController::URL('payments', 'admin', 'modifyconfig', ['tabmodule' => $tabmodule, 'tab' => $data['tab']]));
            // Return
            return true;
            break;
    }
//        $data['hooks'] = $hooks;
    $data['tabmodule'] = $tabmodule;
    $data['authid'] = xarSec::genAuthKey();
    return $data;
}

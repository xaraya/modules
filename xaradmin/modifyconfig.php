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
        if (!xarSecurityCheck('AdminPayments')) return;
        if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
        if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'payments_general', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('tabmodule', 'str:1:100', $tabmodule, 'payments', XARVAR_NOT_REQUIRED)) return;

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

        $data['module_settings'] = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'payments'));
        $data['module_settings']->setFieldList('items_per_page, use_module_alias, enable_short_urls, use_module_icons, frontend_page, backend_page');
        $data['module_settings']->getItem();

        $regid = xarMod::getRegID($tabmodule);
        switch (strtolower($phase)) {
            case 'modify':
            default:
                switch ($data['tab']) {
                    case 'payments_general':
                    default:
                    $object = DataObjectMaster::getObjectList(array('name' => 'payments_gateways'));
                    $data['items'] = $object->getItems(array('where' => 'state eq 3'));
                    break;
                }

                break;

            case 'update':
                // Confirm authorisation code
                if (!xarSecConfirmAuthKey()) return;
                if (!xarVarFetch('customerobject', 'str',      $customerobject, xarModVars::get('payments', 'customerobject'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('orderobject',    'str',      $orderobject, xarModVars::get('payments', 'orderobject'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('gateway',        'int',      $gateway, xarModVars::get('payments', 'gateway'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('runpayments',    'checkbox', $runpayments, xarModVars::get('payments', 'runpayments'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('savetodb',       'checkbox', $savetodb, xarModVars::get('payments', 'savetodb'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('alertemail',     'checkbox', $alertemail, xarModVars::get('payments', 'alertemail'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('alertemailaddr', 'str',      $alertemailaddr, xarModVars::get('payments', 'alertemailaddr'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('defaultcurrency','str',      $defaultcurrency, xarModVars::get('payments', 'defaultcurrency'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('defaultamount',  'float',    $defaultamount, xarModVars::get('payments', 'defaultamount'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('process',        'int',      $process, xarModVars::get('payments', 'process'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('allowanonpay',   'checkbox', $allowanonpay, xarModVars::get('payments', 'allowanonpay'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('payments_active','checkbox', $payments_active, xarModVars::get('payments', 'payments_active'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('message_id',     'int',      $message_id, xarModVars::get('payments', 'message_id'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('message_prefix', 'str',      $message_prefix, xarModVars::get('payments', 'message_prefix'), XARVAR_NOT_REQUIRED)) return;

                $modvars = array(
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
                                );

                if ($data['tab'] == 'payments_general') {
                    $isvalid = $data['module_settings']->checkInput();
                    if (!$isvalid) {
                        return xarTplModule('payments','admin','modifyconfig', $data);
                    } else {
                        $itemid = $data['module_settings']->updateItem();
                    }

                    foreach ($modvars as $var) if (isset($$var)) xarModVars::set('payments', $var, $$var);
                }
                foreach ($modvars as $var) if (isset($$var)) xarModItemVars::set('payments', $var, $$var, $regid);

                if (!xarVarFetch('enable_demomode',    'int', $enable_demomode, 0, XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('demousers', 'str', $demousers, '', XARVAR_NOT_REQUIRED)) return;
                $demousers = explode(',',$demousers);
                $validdemousers = array();
                foreach ($demousers as $demouser) {
                    if (empty($demouser)) continue;
                    $user = xarMod::apiFunc('roles','user','get',array('uname' => trim($demouser)));
                    if(!empty($user)) $validdemousers[$user['uname']] = $user['uname'];
                }
                xarModVars::set('payments', 'enable_demomode', $enable_demomode);
                xarModVars::set('payments', 'demousers', serialize($validdemousers));

                xarController::redirect(xarModURL('payments', 'admin', 'modifyconfig',array('tabmodule' => $tabmodule, 'tab' => $data['tab'])));
                // Return
                return true;
                break;

        }
//        $data['hooks'] = $hooks;
        $data['tabmodule'] = $tabmodule;
        $data['authid'] = xarSecGenAuthKey();
        return $data;
    }
?>
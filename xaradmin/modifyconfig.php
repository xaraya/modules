<?php
/**
 * Otp Module
 *
 * @package modules
 * @subpackage otp
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2017 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Main configuration page for the otp module
 *
 */

// Use this version of the modifyconfig file when the module is not a  utility module

function otp_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurity::check('AdminOtp')) {
        return;
    }
    if (!xarVar::fetch('phase', 'str:1:100', $phase, 'modify', xarVar::NOT_REQUIRED, xarVar::PREP_FOR_DISPLAY)) {
        return;
    }
    if (!xarVar::fetch('tab', 'str:1:100', $data['tab'], 'general', xarVar::NOT_REQUIRED)) {
        return;
    }

    $data['module_settings'] = xarMod::apiFunc('base', 'admin', 'getmodulesettings', array('module' => 'otp'));
    $data['module_settings']->setFieldList('items_per_page, use_module_alias, enable_short_urls, use_module_icons, frontend_page, backend_page');
    $data['module_settings']->getItem();

    // Check which algorithms we have available
    sys::import('modules.otp.xarincludes.php-otp.Otp');
    $otp = new Otp();
    $available_algorithms = $otp->getAvailableAlgorithms();
    $data['available_algorithms'] = array();
    foreach ($available_algorithms as $row) {
        $data['available_algorithms'][] = array('id' => $row, 'name' => $row);
    }
    
    switch (strtolower($phase)) {
        case 'modify':
        default:
            switch ($data['tab']) {
                case 'general':
                    break;
                case 'tab2':
                    break;
                default:
                    break;
            }

            break;

        case 'update':
            // Confirm authorisation code. AJAX calls ignore this
            if (!xarSec::confirmAuthKey()) {
                return xarTpl::module('privileges', 'user', 'errors', array('layout' => 'bad_author'));
            }
            switch ($data['tab']) {
                case 'general':
                    $isvalid = $data['module_settings']->checkInput();
                    if (!$isvalid) {
                        // If this is an AJAX call, send back a message (and end)
                        xarController::$request->msgAjax($data['module_settings']->getInvalids());
                        // No AJAX, just send the data to the template for display
                        return xarTpl::module('otp', 'admin', 'modifyconfig', $data);
                    } else {
                        $itemid = $data['module_settings']->updateItem();
                    }

                    if (!xarVar::fetch('sequence', 'int:1', $sequence, xarModVars::get('otp', 'sequence'), xarVar::NOT_REQUIRED)) {
                        return;
                    }
                    if (!xarVar::fetch('algorithm', 'str:1', $algorithm, xarModVars::get('otp', 'algorithm'), xarVar::NOT_REQUIRED)) {
                        return;
                    }
                    if (!xarVar::fetch('expires', 'int', $expires, xarModVars::get('otp', 'expires'), xarVar::NOT_REQUIRED)) {
                        return;
                    }
                    if (!xarVar::fetch('debugmode', 'checkbox', $debugmode, xarModVars::get('otp', 'debugmode'), xarVar::NOT_REQUIRED)) {
                        return;
                    }

                    xarModVars::set('otp', 'sequence', $sequence);
                    xarModVars::set('otp', 'algorithm', $algorithm);
                    xarModVars::set('otp', 'expires', $expires);
                    xarModVars::set('otp', 'debugmode', $debugmode);
                    break;
                case 'tab2':
                    break;
                default:
                    break;
            }

            // If this is an AJAX call, end here
            xarController::$request->exitAjax();
            xarController::redirect(xarController::URL('otp', 'admin', 'modifyconfig', array('tab' => $data['tab'])));
            return true;
            break;

    }
    $data['authid'] = xarSec::genAuthKey();
    return $data;
}

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
    if (!xarSecurityCheck('AdminOtp')) return;
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'general', XARVAR_NOT_REQUIRED)) return;

    $data['module_settings'] = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'otp'));
    $data['module_settings']->setFieldList('items_per_page, use_module_alias, enable_short_urls, use_module_icons, frontend_page, backend_page');
    $data['module_settings']->getItem();

    // Check which algorithms we have available
    sys::import('modules.otp.xarincludes.php-otp.Otp');
    $otp = new Otp();
    $available_algorithms = $otp->getAvailableAlgorithms();
    $data['available_algorithms'] = array();
    foreach ($available_algorithms as $row) 
        $data['available_algorithms'][] = array('id' => $row, 'name' => $row);
    
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
            if (!xarSecConfirmAuthKey()) {
                return xarTpl::module('privileges','user','errors',array('layout' => 'bad_author'));
            }        
            switch ($data['tab']) {
                case 'general':
                    $isvalid = $data['module_settings']->checkInput();
                    if (!$isvalid) {
                        // If this is an AJAX call, send back a message (and end)
                        xarController::$request->msgAjax($data['module_settings']->getInvalids());
                        // No AJAX, just send the data to the template for display
                        return xarTplModule('otp','admin','modifyconfig', $data);        
                    } else {
                        $itemid = $data['module_settings']->updateItem();
                    }

                    if (!xarVarFetch('sequence',  'int:1',    $sequence,  xarModVars::get('otp', 'sequence'),  XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('algorithm', 'str:1',    $algorithm, xarModVars::get('otp', 'algorithm'), XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('expires',   'int',      $expires,   xarModVars::get('otp', 'expires'),   XARVAR_NOT_REQUIRED)) return;
	                if (!xarVarFetch('debugmode', 'checkbox', $debugmode, xarModVars::get('otp', 'debugmode'), XARVAR_NOT_REQUIRED)) return;

                    xarModVars::set('otp', 'sequence',  $sequence);
                    xarModVars::set('otp', 'algorithm', $algorithm);
                    xarModVars::set('otp', 'expires',   $expires);
	                xarModVars::set('otp', 'debugmode', $debugmode);
                    break;
                case 'tab2':
                    break;
                default:
                    break;
            }

            // If this is an AJAX call, end here
            xarController::$request->exitAjax();
            xarController::redirect(xarModURL('otp', 'admin', 'modifyconfig',array('tab' => $data['tab'])));
            return true;
            break;

    }
    $data['authid'] = xarSecGenAuthKey();
    return $data;
}
?>

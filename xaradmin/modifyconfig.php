<?php
/**
 * Reminders Module
 *
 * @package modules
 * @subpackage reminders
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2019 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Main configuration page for the reminders module
 *
 */

// Use this version of the modifyconfig file when the module is not a  utility module

function reminders_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurity::check('AdminReminders')) return;
    if (!xarVar::fetch('phase', 'str:1:100', $phase, 'modify', xarVar::NOT_REQUIRED, xarVar::PREP_FOR_DISPLAY)) return;
    if (!xarVar::fetch('tab', 'str:1:100', $data['tab'], 'general', xarVar::NOT_REQUIRED)) return;

    $data['module_settings'] = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'reminders'));
    $data['module_settings']->setFieldList('items_per_page, use_module_alias, enable_short_urls, use_module_icons, frontend_page, backend_page');
    $data['module_settings']->getItem();

	$data['subject'] = xarModVars::get('reminders', 'subject');
	$data['message'] = unserialize(xarModVars::get('reminders', 'message'));
	$data['lookup_template'] = xarModVars::get('reminders', 'lookup_template');

    // Get the available email templates
    $data['lookup_options'] = xarMod::apiFunc('mailer' , 'user' , 'getall_mails', array('state' => 3, 'module'=> "reminders", 'category' => xarModVars::get('reminders', 'lookup_emails')));

    switch (strtolower($phase)) {
        case 'modify':
        default:
            switch ($data['tab']) {
                case 'general':
                    break;
                case 'lookups':
                    break;
                default:
                    break;
            }

            break;

        case 'update':
            // Confirm authorisation code. AJAX calls ignore this
            if (!xarSec::confirmAuthKey()) {
                return xarTpl::module('privileges','user','errors',array('layout' => 'bad_author'));
            }        
            switch ($data['tab']) {
                case 'general':
                    $isvalid = $data['module_settings']->checkInput();
                    if (!$isvalid) {
                        // If this is an AJAX call, send back a message (and end)
                        xarController::$request->msgAjax($data['module_settings']->getInvalids());
                        // No AJAX, just send the data to the template for display
                        return xarTpl::module('reminders','admin','modifyconfig', $data);        
                    } else {
                        $itemid = $data['module_settings']->updateItem();
                    }

                    if (!xarVar::fetch('save_history',     'checkbox', $save_history, xarModVars::get('reminders', 'save_history'), xarVar::NOT_REQUIRED)) return;
                    if (!xarVar::fetch('debugmode',        'checkbox', $debugmode,    xarModVars::get('reminders', 'debugmode'), xarVar::NOT_REQUIRED)) return;

                    $modvars = array(
                                    'save_history',
                                    'debugmode',
                                    );

                    foreach ($modvars as $var) if (isset($$var)) xarModVars::set('reminders', $var, $$var);
                    break;
                case 'lookups':
                    if (!xarVar::fetch('subject', 'str', $subject, xarModVars::get('reminders', 'subject'), xarVar::NOT_REQUIRED)) return;
    				$textarea = DataPropertyMaster::getProperty(array('name' => 'textarea'));
    				$textarea->checkInput('message');
					xarModVars::set('reminders', 'message', serialize($textarea->value));
					xarModVars::set('reminders', 'subject', $subject);
                    break;
                default:
                    break;
            }

            // If this is an AJAX call, end here
            xarController::$request->exitAjax();
            xarController::redirect(xarController::URL('reminders', 'admin', 'modifyconfig',array('tab' => $data['tab'])));
            return true;
            break;

    }
    $data['authid'] = xarSec::genAuthKey();
    return $data;
}
?>

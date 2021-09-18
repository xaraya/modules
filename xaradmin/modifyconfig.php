<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 *//**
 * Modify module's configuration
 *
 * This is a standard function to modify the configuration parameters of the
 * module
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return mixed, true on update success, or array of form data
 */
function crispbb_admin_modifyconfig()
{

    // Admin only function
    if (!xarSecurity::check('AdminCrispBB')) {
        return;
    }
    $data = [];
    if (!xarVar::fetch('sublink', 'str:1:', $sublink, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('phase', 'enum:form:update', $phase, 'form', xarVar::NOT_REQUIRED)) {
        return;
    }
    // allow return url to be over-ridden
    if (!xarVar::fetch('return_url', 'str:1:', $data['return_url'], '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('checkupdate', 'checkbox', $checkupdate, false, xarVar::NOT_REQUIRED)) {
        return;
    }

    $now = time();

    // Check the version of this module
    $modid = xarMod::getRegID('crispbb');
    $modinfo = xarMod::getInfo($modid);
    if ($checkupdate) {
        $phase = 'form';
        $hasupdate = xarMod::apiFunc(
            'crispbb',
            'user',
            'checkupdate',
            ['version' => $modinfo['version']]
        );
        // set for waiting content
        if ($hasupdate) {
            xarModVars::set('crispbb', 'latestversion', $hasupdate);
        }
    }

    // Add the standard module settings
    $data['module_settings'] = xarMod::apiFunc('base', 'admin', 'getmodulesettings', ['module' => 'crispbb']);
    $data['module_settings']->setFieldList('use_module_alias, module_alias_name, enable_short_urls');
    $data['module_settings']->setFieldList('items_per_page, use_module_alias, module_alias_name, enable_short_urls', 'frontend_page, backend_page');
    $data['module_settings']->getItem();
    $invalid = [];

    switch (strtolower($phase)) {
        case 'modify':
        default:
            break;

        case 'update':
        $sessionTimeout = xarConfigVars::get(null, 'Site.Session.InactivityTimeout');
        if (!xarVar::fetch('visit_timeout', "int:1:$sessionTimeout", $visit_timeout, $sessionTimeout, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('showuserpanel', 'checkbox', $showuserpanel, false, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('showsearchbox', 'checkbox', $showsearchbox, false, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('showforumjump', 'checkbox', $showforumjump, false, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('showtopicjump', 'checkbox', $showtopicjump, false, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('showquickreply', 'checkbox', $showquickreply, false, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('showpermissions', 'checkbox', $showpermissions, false, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('showsortbox', 'checkbox', $showsortbox, false, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('editor', 'str', $editor, xarModVars::get('crispbb', 'editor'), xarVar::NOT_REQUIRED)) {
            return;
        }

        $isvalid = $data['module_settings']->checkInput();
        if ($isvalid) {
            if (!xarSec::confirmAuthKey()) {
                return xarTpl::module('privileges', 'user', 'errors', ['layout' => 'bad_author']);
            }
            $currentalias = xarModVars::get('crispbb', 'module_alias_name');

            // Update the module settings
            $itemid = $data['module_settings']->updateItem();

            $module = 'crispbb';
            $newalias = trim(xarModVars::get('crispbb', 'module_alias_name'));
            if (empty($newalias)) {
                xarModVars::set('crispbb', 'use_module_alias', 0);
                $usealias = false;
            } else {
                $usealias = xarModVars::get('crispbb', 'use_module_alias');
            }
            if ($usealias && $newalias != $currentalias) {
                if (!empty($currentalias)) {
                    $hasalias = xarModAlias::resolve($currentalias);
                    if (!empty($hasalias) && $hasalias == $module) {
                        xarModAlias::delete($currentalias, $module);
                    }
                }
                if (strpos($newalias, '_') === false) {
                    $newalias = str_replace(' ', '_', $newalias);
                    xarModVars::set($module, 'module_alias_name', $newalias);
                }
                xarModAlias::set($newalias, $module);
            } elseif (!$usealias && !empty($currentalias)) {
                $hasalias = xarModAlias::resolve($currentalias);
                if (!empty($hasalias) && $hasalias == $module) {
                    xarModAlias::delete($currentalias, $module);
                }
            }

            xarModVars::set('crispbb', 'visit_timeout', $visit_timeout);
            xarModVars::set('crispbb', 'showuserpanel', $showuserpanel);
            xarModVars::set('crispbb', 'showsearchbox', $showsearchbox);
            xarModVars::set('crispbb', 'showforumjump', $showforumjump);
            xarModVars::set('crispbb', 'showtopicjump', $showtopicjump);
            xarModVars::set('crispbb', 'showquickreply', $showquickreply);
            xarModVars::set('crispbb', 'showpermissions', $showpermissions);
            xarModVars::set('crispbb', 'showsortbox', $showsortbox);
            xarModVars::set('crispbb', 'editor', $editor);

            xarModHooks::call(
                'module',
                'updateconfig',
                'crispbb',
                ['module' => 'crispbb']
            );
            // update the status message
            xarSession::setVar('crispbb_statusmsg', xarML('Module configuration updated'));
            // if no returnurl specified, return to the modify function for the newly created forum
            if (empty($data['return_url'])) {
                $data['return_url'] = xarController::URL('crispbb', 'admin', 'modifyconfig');
            }
            xarController::redirect($data['return_url']);
            return true;
        }

    }
    $pageTitle = xarML('Modify Module Configuration');
    $data['menulinks'] = xarMod::apiFunc(
        'crispbb',
        'admin',
        'getmenulinks',
        [
            'current_module' => 'crispbb',
            'current_type' => 'admin',
            'current_func' => 'modifyconfig',
            'current_sublink' => $sublink,
        ]
    );

    // Update the display controls
    $data['visit_timeout'] = xarModVars::get('crispbb', 'visit_timeout');
    $data['showuserpanel'] = xarModVars::get('crispbb', 'showuserpanel');
    $data['showsearchbox'] = xarModVars::get('crispbb', 'showsearchbox');
    $data['showforumjump'] = xarModVars::get('crispbb', 'showforumjump');
    $data['showtopicjump'] = xarModVars::get('crispbb', 'showtopicjump');
    $data['showquickreply'] = xarModVars::get('crispbb', 'showquickreply');
    $data['showpermissions'] = xarModVars::get('crispbb', 'showpermissions');
    $data['showsortbox'] = xarModVars::get('crispbb', 'showsortbox');

    // uUpdate he version check
    $data['version'] = $modinfo['version'];
    $data['newversion'] = !empty($hasupdate) ? $hasupdate : null;
    $data['checkupdate'] = $checkupdate;

    // store function name for use by admin-main as an entry point
    xarSession::setVar('crispbb_adminstartpage', 'modifyconfig');
    /* Return the template variables defined in this function */
    xarTpl::setPageTitle(xarVar::prepForDisplay($pageTitle));
    return $data;
}

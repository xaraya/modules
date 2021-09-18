<?php
/**
 * Hitcount Module
 *
 * @package modules
 * @subpackage hitcount module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/177.html
 * @author Hitcount Module Development Team
 */
/**
 * modify configuration
 * @param string phase
 * @return array
 */
function hitcount_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurity::check('AdminHitcount')) {
        return;
    }

    if (!xarVar::fetch('phase', 'str:1:100', $phase, 'modify', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('tab', 'str:1:100', $data['tab'], 'general', xarVar::NOT_REQUIRED)) {
        return;
    }

    $data['module_settings'] = xarMod::apiFunc('base', 'admin', 'getmodulesettings', ['module' => 'hitcount']);
    $data['module_settings']->setFieldList('items_per_page, use_module_alias, module_alias_name, enable_short_urls');
    $data['module_settings']->getItem();

    switch (strtolower($phase)) {
        case 'modify':
        default:
            switch ($data['tab']) {
                case 'general':
                    // Quick Data Array
                    $data['authid'] = xarSec::genAuthKey();
                    $data['numitems'] = xarModVars::get('hitcount', 'numitems');
                    if (empty($data['numitems'])) {
                        $data['numitems'] = 10;
                    }
                    $data['numstats'] = xarModVars::get('hitcount', 'numstats');
                    if (empty($data['numstats'])) {
                        $data['numstats'] = 100;
                    }
                    $data['showtitle'] = xarModVars::get('hitcount', 'showtitle');
                    if (!empty($data['showtitle'])) {
                        $data['showtitle'] = 1;
                    }
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
                return xarTpl::module('privileges', 'user', 'errors', ['layout' => 'bad_author']);
            }
            switch ($data['tab']) {
                case 'general':
                    if (!xarVar::fetch('countadmin', 'checkbox', $countadmin, false, xarVar::NOT_REQUIRED)) {
                        return;
                    }
                    if (!xarVar::fetch('numitems', 'int', $numitems, 10, xarVar::NOT_REQUIRED)) {
                        return;
                    }
                    if (!xarVar::fetch('numstats', 'int', $numstats, 100, xarVar::NOT_REQUIRED)) {
                        return;
                    }
                    if (!xarVar::fetch('showtitle', 'checkbox', $showtitle, false, xarVar::NOT_REQUIRED)) {
                        return;
                    }

                    $isvalid = $data['module_settings']->checkInput();
                    if (!$isvalid) {
                        return xarTpl::module('eventhub', 'admin', 'modifyconfig', $data);
                    } else {
                        $itemid = $data['module_settings']->updateItem();
                    }

                    // Update module variables
                    xarModVars::set('hitcount', 'countadmin', $countadmin);
                    xarModVars::set('hitcount', 'numitems', $numitems);
                    xarModVars::set('hitcount', 'numstats', $numstats);
                    xarModVars::set('hitcount', 'showtitle', $showtitle);
                    xarController::redirect(xarController::URL('hitcount', 'admin', 'modifyconfig'));
                    // Return
                    return true;
                        break;
                    case 'tab2':
                        break;
                    case 'tab3':
                        break;
                    default:
                        break;
            }
            break;
    }

    return $data;
}

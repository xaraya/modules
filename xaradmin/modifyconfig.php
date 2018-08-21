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
    if (!xarSecurityCheck('AdminHitcount')) return;

    if (!xarVarFetch('phase', 'str:1:100', $phase,       'modify',  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tab',   'str:1:100', $data['tab'], 'general', XARVAR_NOT_REQUIRED)) return;

    $data['module_settings'] = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'hitcount'));
    $data['module_settings']->setFieldList('items_per_page, use_module_alias, module_alias_name, enable_short_urls');
    $data['module_settings']->getItem();

    switch (strtolower($phase)) {
        case 'modify':
        default:
            switch ($data['tab']) {
                case 'general':
                    // Quick Data Array
                    $data['authid'] = xarSecGenAuthKey();
                    $data['numitems'] = xarModVars::get('hitcount','numitems');
                    if (empty($data['numitems'])) {
                        $data['numitems'] = 10;
                    }
                    $data['numstats'] = xarModVars::get('hitcount','numstats');
                    if (empty($data['numstats'])) {
                        $data['numstats'] = 100;
                    }
                    $data['showtitle'] = xarModVars::get('hitcount','showtitle');
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
            if (!xarSecConfirmAuthKey()) {
                return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
            }        
            switch ($data['tab']) {
                case 'general':
                    if (!xarVarFetch('countadmin', 'checkbox', $countadmin, false, XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('numitems', 'int', $numitems, 10, XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('numstats', 'int', $numstats, 100, XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('showtitle', 'checkbox', $showtitle, false, XARVAR_NOT_REQUIRED)) return;

                    $isvalid = $data['module_settings']->checkInput();
                    if (!$isvalid) {
                        return xarTplModule('eventhub','admin','modifyconfig', $data);
                    } else {
                        $itemid = $data['module_settings']->updateItem();
                    }

                    // Update module variables
                    xarModVars::set('hitcount', 'countadmin', $countadmin);
                    xarModVars::set('hitcount', 'numitems', $numitems);
                    xarModVars::set('hitcount', 'numstats', $numstats);
                    xarModVars::set('hitcount', 'showtitle', $showtitle);
                    xarController::redirect(xarModURL('hitcount', 'admin', 'modifyconfig'));
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

?>
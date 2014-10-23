<?php
/**
 * Scheduler Module
 *
 * @package modules
 * @subpackage scheduler module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/189.html
 * @author mikespub
 */
/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function scheduler_admin_modifyconfig()
{
// Security Check
    if (!xarSecurityCheck('AdminScheduler')) return;

    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('tab','str:1', $data['tab'], 'general', XARVAR_NOT_REQUIRED)) return;

    switch (strtolower($data['tab'])) {
        case 'general':
            $data['module_settings'] = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'scheduler'));
            $data['module_settings']->setFieldList('items_per_page, use_module_alias, use_module_icons');
            $data['module_settings']->getItem();
        break;
    }

    switch (strtolower($phase)) {
        case 'modify':
        default:
            break;
        case 'update':
            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) {
                return xarTpl::module('privileges','user','errors',array('layout' => 'bad_author'));
            }

            switch (strtolower($data['tab'])) {
                case 'general':
                    $isvalid = $data['module_settings']->checkInput();
                    if (!$isvalid) {
                        return xarTpl::module('scheduler','admin','modifyconfig', $data);
                    } else {
                        $itemid = $data['module_settings']->updateItem();
                    }
                    
                    if (!xarVarFetch('interval', 'str', $interval, '5t', XARVAR_NOT_REQUIRED)) return;
                    xarModVars::set('scheduler', 'interval', $interval);
                break;
            }
        break;
    }
    return $data;
}
?>

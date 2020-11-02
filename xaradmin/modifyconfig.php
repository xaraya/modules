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
    if (!xarSecurity::check('AdminScheduler')) return;

    if (!xarVar::fetch('phase', 'str:1:100', $phase, 'modify', xarVar::NOT_REQUIRED, xarVar::PREP_FOR_DISPLAY)) return;
    if (!xarVar::fetch('tab','str:1', $data['tab'], 'general', xarVar::NOT_REQUIRED)) return;

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
            if (!xarSec::confirmAuthKey()) {
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
                    
                    if (!xarVar::fetch('interval', 'str', $interval, '5t', xarVar::NOT_REQUIRED)) return;
                    xarModVars::set('scheduler', 'interval', $interval);
                break;
            }
        break;
    }
    return $data;
}
?>

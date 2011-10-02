<?php
/**
 * Ephemerids
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Ephemerids Module
 * @link http://xaraya.com/index.php/release/15.html
 * @author Volodymyr Metenchuk
 */
/**
 * modify configuration
 */
function ephemerids_admin_modifyconfig()
{
    // Security Check
    if(!xarSecurityCheck('AdminEphemerids')) return;

    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('tab','str:1', $data['tab'], 'general', XARVAR_NOT_REQUIRED)) return;

    switch (strtolower($data['tab'])) {
        case 'general':
        default:
            $data['module_settings'] = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'ephemerids'));
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
                return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
            }

            switch (strtolower($data['tab'])) {
                case 'general':
                default:
                    $isvalid = $data['module_settings']->checkInput();
                    if (!$isvalid) {
                        return xarTplModule('ephemerids','admin','modifyconfig', $data);
                    } else {
                        $itemid = $data['module_settings']->updateItem();
                    }
                break;
            }
            break;
    }
    return $data;
    xarController::redirect(xarModURL('ephemerids', 'admin', 'modifyconfig'));
}
?>
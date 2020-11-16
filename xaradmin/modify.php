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
 * Modify an item of the otp object
 *
 */
    
function otp_admin_modify()
{
    if (!xarSecurityCheck('EditOtp')) {
        return;
    }

    if (!xarVarFetch('name', 'str', $name, 'otp_otp', XARVAR_NOT_REQUIRED)) {
        return;
    }
    if (!xarVarFetch('itemid', 'int', $data['itemid'], 0, XARVAR_NOT_REQUIRED)) {
        return;
    }
    if (!xarVarFetch('confirm', 'checkbox', $data['confirm'], false, XARVAR_NOT_REQUIRED)) {
        return;
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('name' => $name));
    $data['object']->getItem(array('itemid' => $data['itemid']));

    $data['tplmodule'] = 'otp';
    $data['authid'] = xarSecGenAuthKey('otp');

    if ($data['confirm']) {
    
        // Check for a valid confirmation key
        if (!xarSecConfirmAuthKey()) {
            return;
        }

        // Get the data from the form
        $isvalid = $data['object']->checkInput();
        
        if (!$isvalid) {
            // Bad data: redisplay the form with error messages
            return xarTplModule('otp', 'admin', 'modify', $data);
        } else {
            // Good data: create the item
            $itemid = $data['object']->updateItem(array('itemid' => $data['itemid']));
            
            // Jump to the next page
            xarController::redirect(xarModURL('otp', 'admin', 'view'));
            return true;
        }
    }
    return $data;
}

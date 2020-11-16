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
 * Create a new item of the otp object
 *
 */

function otp_admin_new()
{
    if (!xarSecurity::check('AddOtp')) {
        return;
    }

    if (!xarVar::fetch('name', 'str', $name, 'otp_otp', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('confirm', 'bool', $data['confirm'], false, xarVar::NOT_REQUIRED)) {
        return;
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('name' => $name));
    $data['tplmodule'] = 'otp';
    $data['authid'] = xarSec::genAuthKey('otp');

    if ($data['confirm']) {
    
        // we only retrieve 'preview' from the input here - the rest is handled by checkInput()
        if (!xarVar::fetch('preview', 'str', $preview, null, xarVar::DONT_SET)) {
            return;
        }

        // Check for a valid confirmation key
        if (!xarSec::confirmAuthKey()) {
            return;
        }
        
        // Get the data from the form
        $isvalid = $data['object']->checkInput();
        
        if (!$isvalid) {
            // Bad data: redisplay the form with error messages
            return xarTpl::module('otp', 'admin', 'new', $data);
        } else {
            // Good data: create the item
            $itemid = $data['object']->createItem();
            
            // Jump to the next page
            xarController::redirect(xarController::URL('otp', 'admin', 'view'));
            return true;
        }
    }
    return $data;
}

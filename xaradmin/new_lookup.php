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
 * Create a new item of the reminders_lookups object
 *
 */

function reminders_admin_new_lookup()
{
    if (!xarSecurityCheck('AddReminders')) {
        return;
    }

    if (!xarVarFetch('name', 'str', $name, 'reminders_lookups', XARVAR_NOT_REQUIRED)) {
        return;
    }
    if (!xarVarFetch('confirm', 'checkbox', $data['confirm'], false, XARVAR_NOT_REQUIRED)) {
        return;
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(['name' => $name]);
    $data['tplmodule'] = 'reminders';
    $data['authid'] = xarSecGenAuthKey('reminders');

    if ($data['confirm']) {

        // we only retrieve 'preview' from the input here - the rest is handled by checkInput()
        if (!xarVarFetch('preview', 'str', $preview, null, XARVAR_DONT_SET)) {
            return;
        }

        // Check for a valid confirmation key
        if (!xarSecConfirmAuthKey()) {
            return;
        }

        // Get the data from the form
        $isvalid = $data['object']->checkInput();

        if (!$isvalid) {
            // Bad data: redisplay the form with error messages
            return xarTplModule('reminders', 'admin', 'new_lookup', $data);
        } else {
            // Good data: proceed
            // First generate the code for this item
            //			$code = xarMod::apiFunc('reminders', 'admin', 'generate_code', array('object' => $data['object']));
            // Add it to the object
//            $data['object']->properties['code']->value = $code;
            // Now create the item
            $itemid = $data['object']->createItem();

            // Jump to the next page
            xarController::redirect(xarModURL('reminders', 'admin', 'view_lookups'));
            return true;
        }
    }
    return $data;
}

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
 * Modify an item of the reminders_lookups object
 *
 */

function reminders_admin_modify_lookup()
{
    if (!xarSecurityCheck('EditReminders')) {
        return;
    }

    if (!xarVarFetch('name', 'str', $name, 'reminders_lookups', XARVAR_NOT_REQUIRED)) {
        return;
    }
    if (!xarVarFetch('itemid', 'int', $data['itemid'], 0, XARVAR_NOT_REQUIRED)) {
        return;
    }
    if (!xarVarFetch('confirm', 'checkbox', $data['confirm'], false, XARVAR_NOT_REQUIRED)) {
        return;
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(['name' => $name]);
    $data['object']->getItem(['itemid' => $data['itemid']]);

    $data['tplmodule'] = 'reminders';
    $data['authid'] = xarSecGenAuthKey('reminders');

    if ($data['confirm']) {

        // Check for a valid confirmation key
        if (!xarSecConfirmAuthKey()) {
            return;
        }

        // Get the data from the form
        $isvalid = $data['object']->checkInput();

        if (!$isvalid) {
            // Bad data: redisplay the form with error messages
            return xarTplModule('reminders', 'admin', 'modify_lookup', $data);
        } else {
            // Good data: create the item
            // Update the time_modified field
            $data['object']->properties['time_modified']->value = time();
            // Save the item
            $itemid = $data['object']->updateItem(['itemid' => $data['itemid']]);

            // Jump to the next page
            xarController::redirect(xarModURL('reminders', 'admin', 'view_lookups'));
            return true;
        }
    }
    return $data;
}

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
 * Create a new item of the reminders_entries object
 *
 */

function reminders_admin_new_entry()
{
    if (!xarSecurity::check('AddReminders')) {
        return;
    }

    if (!xarVar::fetch('name', 'str', $name, 'reminders_entries', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('confirm', 'checkbox', $data['confirm'], false, xarVar::NOT_REQUIRED)) {
        return;
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(['name' => $name]);
    $data['tplmodule'] = 'reminders';
    $data['authid'] = xarSec::genAuthKey('reminders');

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
            return xarTpl::module('reminders', 'admin', 'new_entry', $data);
        } else {
            // Good data: proceed
            // First generate the code for this item
            $code = xarMod::apiFunc('reminders', 'admin', 'generate_code', ['object' => $data['object']]);
            // Add it to the object
            $data['object']->properties['code']->value = $code;
            // Now create the item
            $itemid = $data['object']->createItem();

            // Jump to the next page
            xarController::redirect(xarController::URL('reminders', 'admin', 'view_entries'));
            return true;
        }
    }
    return $data;
}

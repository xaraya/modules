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
 * Modify an item of the reminders_entries object
 *
 */
    
function reminders_admin_modify_email()
{
    if (!xarSecurity::check('EditReminders')) {
        return;
    }

    if (!xarVar::fetch('name', 'str', $name, 'reminders_emails', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('itemid', 'int', $data['itemid'], 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('confirm', 'checkbox', $data['confirm'], false, xarVar::NOT_REQUIRED)) {
        return;
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('name' => $name));
    $data['object']->getItem(array('itemid' => $data['itemid']));

    $data['tplmodule'] = 'reminders';
    $data['authid'] = xarSec::genAuthKey('reminders');

    if ($data['confirm']) {
    
        // Check for a valid confirmation key
        if (!xarSec::confirmAuthKey()) {
            return;
        }

        // Get the data from the form
        $isvalid = $data['object']->checkInput();
        
        if (!$isvalid) {
            // Bad data: redisplay the form with error messages
            return xarTpl::module('reminders', 'admin', 'modify_email', $data);
        } else {
            // Good data: proceed
            // Update the time_modified field
            $data['object']->properties['time_modified']->value = time();
            // Save the item
            $itemid = $data['object']->updateItem(array('itemid' => $data['itemid']));
            
            // Jump to the next page
            xarController::redirect(xarController::URL('reminders', 'admin', 'view_emails'));
            return true;
        }
    }
    return $data;
}

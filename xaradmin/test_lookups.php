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
 * Test the lookups process
 *
 */

function reminders_admin_test_lookups()
{
    if (!xarSecurityCheck('ManageReminders')) {
        return;
    }

    if (!xarVarFetch('confirm', 'checkbox', $data['confirm'], false, XARVAR_NOT_REQUIRED)) {
        return;
    }

    if ($data['confirm']) {

        // Check for a valid confirmation key
        if (!xarSecConfirmAuthKey()) {
            return;
        }

        // Check if we send a bcc of the email(s)
        $checkbox = DataPropertyMaster::getProperty(['name' => 'checkbox']);
        $checkbox->checkInput('copy_emails');
        $bccaddress = $checkbox->value ? [xarUser::getVar('email')] : [];

        $data['results'] = xarMod::apiFunc('reminders', 'admin', 'process_lookups', ['test' => true, 'copy_emails' => $bccaddress]);
        $data['test'] = true;
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(['name' => 'reminders_lookups']);
    $data['object']->dataquery->eq('state', 3);

    return $data;
}

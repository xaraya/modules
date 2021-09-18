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
 * Test the reminders process
 *
 */

function reminders_admin_test()
{
    if (!xarSecurity::check('ManageReminders')) {
        return;
    }

    if (!xarVar::fetch('confirm', 'checkbox', $data['confirm'], false, xarVar::NOT_REQUIRED)) {
        return;
    }

    if ($data['confirm']) {

        // Check for a valid confirmation key
        if (!xarSec::confirmAuthKey()) {
            return;
        }

        // Check if we get a copy of the email(s)
        $checkbox = DataPropertyMaster::getProperty(['name' => 'checkbox']);
        $checkbox->checkInput('copy_emails');
        $bccaddress = $checkbox->value ? [xarUser::getVar('email')] : [];

        $data['results'] = xarMod::apiFunc('reminders', 'admin', 'process', ['test' => true, 'copy_emails' => $bccaddress]);
        $data['test'] = true;
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(['name' => 'reminders_entries']);
    $data['object']->dataquery->eq('state', 3);

    return $data;
}

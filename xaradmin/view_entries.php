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
 * View items of the reminders object
 *
 */
function reminders_admin_view_entries($args)
{
    if (!xarSecurity::check('ManageReminders')) {
        return;
    }

    $modulename = 'reminders';

    // Define which object will be shown
    if (!xarVar::fetch('objectname', 'str', $data['objectname'], 'reminders_entries', xarVar::DONT_SET)) {
        return;
    }

    return $data;
}

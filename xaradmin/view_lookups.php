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
 * View items of the lookups object
 *
 */
function reminders_admin_view_lookups($args)
{
    if (!xarSecurity::check('ManageReminders')) {
        return;
    }

    $modulename = 'reminders';

    // Define which object will be shown
    if (!xarVar::fetch('objectname', 'str', $data['objectname'], 'reminders_lookups', xarVar::DONT_SET)) {
        return;
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(['name' => 'reminders_lookups']);
    $data['object']->dataquery->eq('state', 3);
    return $data;
}

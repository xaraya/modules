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
    if (!xarSecurityCheck('ManageReminders')) return;

    $modulename = 'reminders';

    // Define which object will be shown
    if (!xarVarFetch('objectname', 'str', $data['objectname'], 'reminders_entries', XARVAR_DONT_SET)) return;

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('name' => 'reminders_entries'));
    $data['object']->dataquery->eq('state', 3);
    return $data;
}
?>
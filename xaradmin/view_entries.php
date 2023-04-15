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
    if (!xarSecurity::check('ManageReminders')) return;

    $modulename = 'reminders';

    if (!xarVar::fetch('tab',         'str:1:100', $data['tab'],       'active', xarVar::NOT_REQUIRED)) return;

    // Define which object will be shown
    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObjectList(array('name' => 'reminders_entries'));
    
    // Add filters
    if ($data['tab'] == 'active') {
        $data['object']->dataquery->gt($data['object']->properties['due_date']->source, time());
    }

    // Create the listing object itself
    $data['listing'] = DatapropertyMaster::getProperty(array(
                                                'name'          => 'listing',
                                                'object'        => $data['object'],
                                                'fieldlist'     => 'message,email_1,email_2,template,due_date,recurring,recur_period,state',
                                                'tplmodule'     => "reminders",
                                                'show_alphabet' => 0,
                                                ));
    // If this is an AJAX call, run it (and exit)
    $data['listing']->ajaxRefresh(array(
                                'tab' => $data['tab'],
                                ));
    
    return $data;
}
?>
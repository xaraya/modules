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
 * Return the options for the admin menu
 *
 */

// TODO: turn this into an xml file
function reminders_dataapi_adminmenu()
{
    return array(
        array('includes' => array('main','overview'), 'target' => 'main', 'label' => xarML('Reminders Overview')),
        array('mask' => 'ManageReminders', 'includes' => 'view', 'target' => 'view', 'title' => xarML('Manage the master tables of thsi module'), 'label' => xarML('Master Tables')),
        array('mask' => 'AdminReminders', 'includes' => 'modifyconfig', 'target' => 'modifyconfig', 'title' => xarML('Modify the Reminders configuration'), 'label' => xarML('Modify Config')),
    );
}
?>
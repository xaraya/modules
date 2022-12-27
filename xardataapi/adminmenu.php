<?php
/**
 * EAV Module
 *
 * @package modules
 * @subpackage eav
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2013 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Return the options for the admin menu
 *
 */

// TODO: turn this into an xml file
function eav_dataapi_adminmenu()
{
    return [
        ['includes' => ['main','overview'], 'target' => 'main', 'label' => xarML('EAV Overview')],
        ['mask' => 'ManageEAV', 'includes' => 'view', 'target' => 'view', 'title' => xarML('Manage the master tables of thsi module'), 'label' => xarML('Master Tables')],
        ['mask' => 'AdminEAV', 'includes' => 'modifyconfig', 'target' => 'modifyconfig', 'title' => xarML('Modify the EAV configuration'), 'label' => xarML('Modify Config')],
    ];
}

<?php
/**
 * Mailer Module
 *
 * @package modules
 * @subpackage mailer module
 * @copyright (C) 2010 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Return the options for the admin menu
 *
 */

// TODO: turn this into an xml file
    function mailer_dataapi_adminmenu()
    {
        if(!xarModVars::get('modules','disableoverview')) {
            return array(
                array('includes' => array('main','overview'), 'target' => 'main', 'label' => xarML('Mailer Overview')),
                array('mask' => 'ManageMailer', 'includes' => 'view', 'target' => 'view', 'title' => xarML('Manage the master tables of this module'), 'label' => xarML('Master Tables')),
                array('mask' => 'AdminMailer', 'includes' => 'modifyconfig', 'target' => 'modifyconfig', 'title' => xarML('Modify the Mailer configuration'), 'label' => xarML('Modify Config')),
            );
        } else {
            return array(
                array('mask' => 'ManageMailer', 'includes' => 'view', 'target' => 'view', 'title' => xarML('Manage the master tables of this module'), 'label' => xarML('Master Tables')),
                array('mask' => 'AdminMailer', 'includes' => 'modifyconfig', 'target' => 'modifyconfig', 'title' => xarML('Modify the Mailer configuration'), 'label' => xarML('Modify Config')),
            );
        }
    }
?>
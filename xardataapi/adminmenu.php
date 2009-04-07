<?php
/**
 * Return the options for the admin menu
 *
 */

// TODO: turn this into an xml file
    function mailer_dataapi_adminmenu()
    {
        return array(
            array('includes' => array('main','overview'), 'target' => 'main', 'label' => xarML('Mailer Overview')),
            array('mask' => 'ManageMailer', 'includes' => 'view', 'target' => 'view', 'title' => xarML('Manage the master tables of this module'), 'label' => xarML('Master Tables')),
            array('mask' => 'AdminMailer', 'includes' => 'modifyconfig', 'target' => 'modifyconfig', 'title' => xarML('Modify the Mailer configuration'), 'label' => xarML('Modify Config')),
        );
    }
?>
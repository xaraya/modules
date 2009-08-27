<?php
/**
 * Return the options for the admin menu
 *
 */

// TODO: turn this into an xml file
    function xarayatesting_dataapi_adminmenu()
    {
        return array(
            array('includes' => array('main','overview'), 'target' => 'main', 'label' => xarML('Xarayatesting Overview')),
//            array('mask' => 'EditXarayatesting', 'includes' => 'view', 'target' => 'sitetests', 'title' => xarML('Display the site test suites'), 'label' => xarML('Site Tests')),
            array('mask' => 'EditXarayatesting', 'includes' => 'view', 'target' => 'testpage', 'title' => xarML('Run the unit test suites'), 'label' => xarML('Run Xaraya Unit Tests')),
            array('mask' => 'EditXarayatesting', 'target' => 'othertests', 'title' => xarML('Run installation scans'), 'label' => xarML('Run Other Xaraya Tests')),
//            array('mask' => 'ManageXarayatesting', 'includes' => 'view', 'target' => 'view', 'title' => xarML('Manage the master tables of thsi module'), 'label' => xarML('Master Tables')),
            array('mask' => 'AdminXarayatesting', 'includes' => 'modifyconfig', 'target' => 'modifyconfig', 'title' => xarML('Modify the Xarayatesting configuration'), 'label' => xarML('Modify Config')),
        );
    }
?>
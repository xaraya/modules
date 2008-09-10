<?php
/**
 * Return the options for the admin menu
 *
 */

// TODO: turn this into an xml file
    function foo_dataapi_adminmenu()
    {
        return array(
            array('includes' => array('main','overview'), 'target' => 'main', 'label' => xarML('Foo Overview')),
            array('mask' => 'ManageFoo', 'includes' => 'view', 'target' => 'view', 'title' => xarML('Manage the master tables of thsi module'), 'label' => xarML('Master Tables')),
            array('mask' => 'AdminFoo', 'includes' => 'modifyconfig', 'target' => 'modifyconfig', 'title' => xarML('Modify the Foo configuration'), 'label' => xarML('Modify Config')),
        );
    }
?>